<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) SILARHI <dev@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb;

use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\Cfonb120\Line01Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line04Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line05Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line07Parser;
use Silarhi\Cfonb\Parser\Cfonb120\NewBalance;
use Silarhi\Cfonb\Parser\EmptyParser;
use Silarhi\Cfonb\Parser\FileParser;

use function sprintf;

final readonly class Cfonb120Reader
{
    public const LINE_LENGTH = 120;

    private FileParser $fileParser;

    public function __construct(?FileParser $fileParser = null)
    {
        $fileParser ??= new FileParser(
            new Line01Parser(),
            new Line04Parser(),
            new Line05Parser(),
            new Line07Parser(),
            new EmptyParser()
        );

        $this->fileParser = $fileParser;
    }

    /** @return Statement[] */
    public function parse(string $content, bool $strict = true): array
    {
        $statementList = [];
        $lastOperation = null;
        // The statement opened by a "01" old balance, until its "07" new balance closes it.
        $statement = null;

        foreach ($this->fileParser->parse($content, self::LINE_LENGTH, $strict) as $result) {
            if ($result instanceof Balance) {
                if (null !== $statement) {
                    throw new ParseException(sprintf('Unable to open a statement with the "01" old balance of account %s dated %s: the statement of account %s opened on %s has no "07" new balance record', $result->getAccountNumber(), $result->getDate()->format('Y-m-d'), $statement->getOldBalance()->getAccountNumber(), $statement->getOldBalance()->getDate()->format('Y-m-d')));
                }

                $lastOperation = null;
                $statement = new Statement();
                $statement->setOldBalance($result);
            } elseif ($result instanceof NewBalance) {
                if (null === $statement) {
                    throw new ParseException(sprintf('Unable to close a statement with the "07" new balance of account %s dated %s: missing "01" old balance record before it', $result->balance->getAccountNumber(), $result->balance->getDate()->format('Y-m-d')));
                }

                $lastOperation = null;
                $statement->setNewBalance($result->balance);
                $statementList[] = $statement;
                $statement = null;
            } elseif ($result instanceof Operation) {
                if (null === $statement) {
                    throw new ParseException(sprintf('Unable to attach the operation with code %s dated %s: missing "01" old balance record before it', $result->getCode(), $result->getDate()->format('Y-m-d')));
                }

                $lastOperation = $result;
                $statement->addOperation($result);
            } elseif ($result instanceof OperationDetail) {
                if (null === $lastOperation) {
                    throw new ParseException(sprintf('Unable to attach a detail for operation with code %s', $result->getCode()));
                }

                $lastOperation->addDetails($result);
            }
        }

        if (null !== $statement) {
            throw new ParseException(sprintf('Unable to close the statement of account %s opened on %s: missing "07" new balance record at the end of the file', $statement->getOldBalance()->getAccountNumber(), $statement->getOldBalance()->getDate()->format('Y-m-d')));
        }

        return $statementList;
    }
}

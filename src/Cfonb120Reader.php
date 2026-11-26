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

use Generator;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Element;
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

    /**
     * Reads the statements of a CFONB 120 file.
     *
     * @return list<Statement>
     *
     * @throws ParseException on the first line that fails to parse, or record out of order
     */
    public function parse(string $content, bool $strict = true): array
    {
        return iterator_to_array($this->build($this->fileParser->parse($content, self::LINE_LENGTH, $strict)), false);
    }

    /**
     * @param iterable<Element> $elements
     *
     * @return Generator<int, Statement>
     */
    private function build(iterable $elements): Generator
    {
        // The statement opened by a "01" old balance, until its "07" new balance closes it.
        $oldBalance = null;
        /** @var list<Operation> $operations */
        $operations = [];
        /** @var list<OperationDetail> $details the details of the last operation */
        $details = [];

        foreach ($elements as $result) {
            if ($result instanceof Balance) {
                if (null !== $oldBalance) {
                    throw new ParseException(sprintf('Unable to open a statement with the "01" old balance of account %s dated %s: the statement of account %s opened on %s has no "07" new balance record', $result->getAccountNumber(), $result->getDate()->format('Y-m-d'), $oldBalance->getAccountNumber(), $oldBalance->getDate()->format('Y-m-d')));
                }

                $oldBalance = $result;
            } elseif ($result instanceof NewBalance) {
                if (null === $oldBalance) {
                    throw new ParseException(sprintf('Unable to close a statement with the "07" new balance of account %s dated %s: missing "01" old balance record before it', $result->balance->getAccountNumber(), $result->balance->getDate()->format('Y-m-d')));
                }

                yield new Statement($oldBalance, $result->balance, $this->withDetails($operations, $details));
                $oldBalance = null;
                $operations = [];
                $details = [];
            } elseif ($result instanceof Operation) {
                if (null === $oldBalance) {
                    throw new ParseException(sprintf('Unable to attach the operation with code %s dated %s: missing "01" old balance record before it', $result->getCode(), $result->getDate()->format('Y-m-d')));
                }

                $operations = $this->withDetails($operations, $details);
                $operations[] = $result;
                $details = [];
            } elseif ($result instanceof OperationDetail) {
                // A detail belongs to the last operation of the open statement.
                if ([] === $operations) {
                    throw new ParseException(sprintf('Unable to attach a detail for operation with code %s', $result->getCode()));
                }

                $details[] = $result;
            }
        }

        if (null !== $oldBalance) {
            throw new ParseException(sprintf('Unable to close the statement of account %s opened on %s: missing "07" new balance record at the end of the file', $oldBalance->getAccountNumber(), $oldBalance->getDate()->format('Y-m-d')));
        }
    }

    /**
     * The operations, with the details given to the last one.
     *
     * @param list<Operation>       $operations
     * @param list<OperationDetail> $details
     *
     * @return list<Operation>
     */
    private function withDetails(array $operations, array $details): array
    {
        $last = array_key_last($operations);
        if (null !== $last && [] !== $details) {
            $operations[$last] = $operations[$last]->withDetails(...$operations[$last]->getDetails(), ...$details);
        }

        return $operations;
    }
}

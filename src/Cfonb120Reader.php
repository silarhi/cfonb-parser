<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
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
use Silarhi\Cfonb\Parser\EmptyParser;
use Silarhi\Cfonb\Parser\FileParser;

class Cfonb120Reader
{
    const LINE_LENGTH = 120;

    /** @var FileParser */
    private $fileParser;

    public function __construct(?FileParser $fileParser = null)
    {
        if (null === $fileParser) {
            $fileParser = new FileParser(
                new Line01Parser(),
                new Line04Parser(),
                new Line05Parser(),
                new Line07Parser(),
                new EmptyParser()
            );
        }

        $this->fileParser = $fileParser;
    }

    /** @return Statement[] */
    public function parse(string $content): array
    {
        $statementList = [];
        $lastOperation = null;
        $statement = new Statement();

        foreach ($this->fileParser->parse($content, self::LINE_LENGTH) as $result) {
            if ($result instanceof Balance) {
                $lastOperation = null;

                if (false === $statement->hasOldBalance()) {
                    $statement->setOldBalance($result);
                } else {
                    $statement->setNewBalance($result);
                    $statementList[] = $statement;
                    $statement = new Statement();
                }
            } elseif ($result instanceof Operation) {
                $lastOperation = $result;
                $statement->addOperation($result);
            } elseif ($result instanceof OperationDetail) {
                if (null === $lastOperation) {
                    throw new ParseException(sprintf('Unable to attach a detail for operation with code %s', $result->getCode()));
                }

                $lastOperation->addDetails($result);
            }
        }

        return $statementList;
    }
}

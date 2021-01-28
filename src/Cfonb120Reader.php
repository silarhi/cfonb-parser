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
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\Cfonb120\EmptyParser;
use Silarhi\Cfonb\Parser\Cfonb120\Line01Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line04Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line05Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line07Parser;

use function chunk_split;
use function explode;
use function sprintf;
use function strlen;
use function strpos;

class Cfonb120Reader
{
    /** @var ParserInterface[] */
    private $parsers;

    public function __construct()
    {
        $this->parsers = [
            new Line01Parser(),
            new Line04Parser(),
            new Line05Parser(),
            new Line07Parser(),
            new EmptyParser(),
        ];
    }

    /** @return Statement[] */
    public function parse(string $content): array
    {
        if (! empty($content) && strlen($content) > 120 && strpos($content, "\n") === false) {
            $content = chunk_split($content, 120, "\n");
        }

        $statementList = [];
        $lines         = explode("\n", $content);
        $statement     = new Statement();
        $lastOperation = null;

        foreach ($lines as $line) {
            $result = $this->findSupportedParserForLine($line)->parse($line);

            if ($result instanceof Balance) {
                $lastOperation = null;
                if ($statement->hasOldBalance() === false) {
                    $statement->setOldBalance($result);
                } else {
                    $statement->setNewBalance($result);
                    $statementList[] = $statement;
                    $statement       = new Statement();
                }
            } elseif ($result instanceof Operation) {
                $lastOperation = $result;
                $statement->addOperation($result);
            } elseif ($result instanceof OperationDetail) {
                if ($lastOperation === null) {
                    throw new ParseException(sprintf('Unable to attach a detail for operation with internal code %s', $result->getInternalCode()));
                }

                $lastOperation->addDetails($result);
            }
        }

        return $statementList;
    }

    private function findSupportedParserForLine(string $line): ParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($line)) {
                return $parser;
            }
        }

        throw new ParseException(sprintf("Unable to find a parser for the line :\n\"%s\"", $line));
    }
}

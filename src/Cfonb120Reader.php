<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
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

class Cfonb120Reader extends AbstractReader
{
    /** @var Statement[] */
    private $statements = [];

    public function __construct()
    {
        $this->parsers = [
            new Line01Parser(),
            new Line04Parser(),
            new Line05Parser(),
            new Line07Parser(),
        ];
    }

    public function parse($content)
    {
        $lines = explode("\n", $content);

        if (0 === \count($lines)) {
            return;
        }

        $statement = new Statement();

        /** @var Operation|null $lastOperation */
        $lastOperation = null;
        foreach ($lines as $line) {
            foreach ($this->parsers as $parser) {
                if (!$parser->supports($line)) {
                    continue;
                }

                $result = $parser->parse($line);
                if ($result instanceof Balance) {
                    $lastOperation = null;
                    if (null === $statement->getOldBalance()) {
                        $statement->setOldBalance($result);
                    } else {
                        $statement->setNewBalance($result);
                        $this->statements[] = $statement;
                        $statement = new Statement();
                    }
                } elseif ($result instanceof Operation) {
                    $lastOperation = $result;
                    $statement->addOperation($result);
                } elseif ($result instanceof OperationDetail) {
                    if (null === $lastOperation) {
                        throw new ParseException(sprintf('Unable to attach a detail for operation with internal code %s', $result->getInternalCode()));
                    }
                    if (null !== $lastOperation->getDetails()) {
                        throw new ParseException(sprintf('The operation with internal code %s already have a detail!', $result->getInternalCode()));
                    }

                    $lastOperation->setDetails($result);
                }

                continue 2;
            }
            throw new ParseException(sprintf("Unable to find a parser for the line :\n###%s###", $line));
        }
    }

    /**
     * @return Statement[]
     */
    public function getStatements(): array
    {
        return $this->statements;
    }
}

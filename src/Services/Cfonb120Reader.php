<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Services;

use Silarhi\Cfonb\Builders\Cfonb120\StatementBuilder;
use Silarhi\Cfonb\Contracts\Cfonb120\OperationInterface;
use Silarhi\Cfonb\Contracts\CfonbReaderInterface;
use Silarhi\Cfonb\Contracts\LineParserInterface;
use Silarhi\Cfonb\Models\Cfonb120\NewBalance;
use Silarhi\Cfonb\Models\Cfonb120\OldBalance;
use Silarhi\Cfonb\Models\Cfonb120\Operation;
use Silarhi\Cfonb\Models\Cfonb120\Detail;
use Silarhi\Cfonb\Models\Cfonb120\Statement;
use Silarhi\Cfonb\Parser\Cfonb120\Line01Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line04Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line05Parser;
use Silarhi\Cfonb\Parser\Cfonb120\Line07Parser;

/**
 * @internal
 */
class Cfonb120Reader extends AbstractReader implements CfonbReaderInterface
{
    /** @var LineParserInterface[] */
    protected $lineParsers = [];

    /** @var int */
    private $lineLength;

    /**
     * @var StatementBuilder
     */
    private $statementBuilder;

    public function __construct()
    {
        $this->lineParsers = [
            new Line01Parser(),
            new Line04Parser(),
            new Line05Parser(),
            new Line07Parser(),
        ];
        $this->lineLength = 120;
        $this->statementBuilder = new StatementBuilder();
    }

    public function parse(string $content): array
    {
        $lines = $this->getLines($content);

        $statements = [];
        foreach ($lines as $line) {
            $lineParser = $this->resolveLineParser($line);
            $result = $lineParser->parse($line);

            if ($result instanceof OldBalance) {
                $this->statementBuilder->createInstance()->putOldBalance($result);
            } elseif ($result instanceof NewBalance) {
                $statements[] = $this->statementBuilder->putNewBalance($result)->popInstance();
            } elseif ($result instanceof Operation) {
                $this->statementBuilder->addOperation($result);
            } elseif ($result instanceof Detail) {
                $this->statementBuilder->lastOperationAddDetail($result);
            }
        }

        return $statements;
    }

    protected function getLineLength(): int
    {
        return $this->lineLength;
    }

    protected function getLineParsers(): array
    {
        return $this->lineParsers;
    }
}

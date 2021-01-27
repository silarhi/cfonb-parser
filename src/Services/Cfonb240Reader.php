<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Services;

use Silarhi\Cfonb\Builders\Cfonb240\TransferBuilder;
use Silarhi\Cfonb\Contracts\CfonbReaderInterface;
use Silarhi\Cfonb\Contracts\LineParserInterface;
use Silarhi\Cfonb\Models\Cfonb240\Header;
use Silarhi\Cfonb\Models\Cfonb240\Transaction;
use Silarhi\Cfonb\Models\Cfonb240\Total;
use Silarhi\Cfonb\Parser\Cfonb240\Line31Parser;
use Silarhi\Cfonb\Parser\Cfonb240\Line34Parser;
use Silarhi\Cfonb\Parser\Cfonb240\Line39Parser;

/**
 * @internal
 */
class Cfonb240Reader extends AbstractReader implements CfonbReaderInterface
{

    /** @var LineParserInterface[] */
    protected $lineParsers = [];

    /** @var int */
    private $lineLength;

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    public function __construct()
    {
        $this->lineParsers = [
            new Line31Parser(),
            new Line34Parser(),
            new Line39Parser(),
        ];
        $this->lineLength = 240;
        $this->transferBuilder = new TransferBuilder();
    }

    public function parse(string $content): array
    {
        $lines = $this->getLines($content);

        /* @var \Silarhi\Cfonb\Contracts\ReadItemInterface[] $transfers */
        $transfers = [];

        foreach ($lines as $line) {
            $lineParser = $this->resolveLineParser($line);
            $result = $lineParser->parse($line);

            if ($result instanceof Header) {
                $this->transferBuilder->createInstance()->putHeader($result);
            } elseif ($result instanceof Total) {
                $transfers[] = $this->transferBuilder->putTotal($result)->popInstance();
            } elseif ($result instanceof Transaction) {
                $this->transferBuilder->addTransaction($result);
            }
        }

        return $transfers;
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

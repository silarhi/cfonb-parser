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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Parser\AmountParser;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;

class Line04Parser extends AbstractCfonb120Parser
{
    /** @var LineParser */
    private $lineParser;
    /** @var DateParser */
    private $parseDate;
    /** @var AmountParser */
    private $parseAmount;

    public function __construct()
    {
        $this->lineParser  = new LineParser();
        $this->parseDate   = new DateParser();
        $this->parseAmount = new AmountParser();
    }

    public function parse(string $content): Operation
    {
        $infos = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [LineParser::NUMERIC, 5],
            'internal_code' => [LineParser::ALPHANUMERIC_BLANK, 4],
            'desk_code' => [LineParser::NUMERIC, 5],
            'currency_code' => [LineParser::ALPHA_BLANK, 3],
            'nb_of_dec' => [LineParser::NUMERIC_BLANK, 1],
            '_unused_1' => [LineParser::ALL, 1],
            'account_nb' => [LineParser::ALPHANUMERIC, 11],
            'operation_code' => [LineParser::ALPHANUMERIC, 2],
            'operation_date' => [LineParser::NUMERIC, 6],
            'reject_code' => [LineParser::NUMERIC_BLANK, 2],
            'value_date' => [LineParser::NUMERIC, 6],
            'label' => [LineParser::ALL, 31],
            '_unused_2' => [LineParser::ALL, 2],
            'reference' => [LineParser::ALL, 7],
            'exempt_code' => [LineParser::ALL, 1],
            '_unused_3' => [LineParser::ALL, 1],
            'amount' => [LineParser::AMOUNT, 13],
            '_unused_5' => [LineParser::ALL, 16],
        ]);

        return new Operation(
            $infos['bank_code'],
            $infos['desk_code'],
            $infos['account_nb'],
            $infos['operation_code'],
            $this->parseDate->parse($infos['operation_date']),
            $this->parseDate->parse($infos['value_date']),
            $infos['label'],
            $infos['reference'],
            $this->parseAmount->parse($infos['amount'], (int) $infos['nb_of_dec']),
            $infos['internal_code'],
            $infos['currency_code'],
            $infos['reject_code'],
            $infos['exempt_code']
        );
    }

    protected function getSupportedCode(): string
    {
        return '04';
    }
}

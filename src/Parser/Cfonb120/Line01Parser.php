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

use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Parser\AmountParser;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;

/** @internal  */
class Line01Parser extends AbstractCfonb120Parser
{
    /**
     * @var LineParser
     */
    private $lineParser;
    /**
     * @var DateParser
     */
    private $parseDate;
    /**
     * @var AmountParser
     */
    private $parseAmount;

    public function __construct()
    {
        $this->lineParser = new LineParser();
        $this->parseDate = new DateParser();
        $this->parseAmount = new AmountParser();
    }

    public function parse(string $content): Balance
    {
        $infos = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [LineParser::NUMERIC, 5],
            '_unused_1' => [LineParser::BLANK, 4],
            'desk_code' => [LineParser::NUMERIC, 5],
            'currency_code' => [LineParser::ALPHA_BLANK, 3],
            'nb_of_dec' => [LineParser::NUMERIC_BLANK, 1],
            '_unused_2' => [LineParser::BLANK, 1],
            'account_nb' => [LineParser::ALPHANUMERIC, 11],
            '_unused_3' => [LineParser::BLANK, 2],
            'date' => [LineParser::NUMERIC, 6],
            '_unused_4' => [LineParser::ALL, 50],
            'amount' => [LineParser::AMOUNT, 13],
            '_unused_5' => [LineParser::ALL, 16],
        ]);

        return new Balance(
            $infos['bank_code'],
            $infos['desk_code'],
            $infos['currency_code'],
            $infos['account_nb'],
            $this->parseDate->parse($infos['date']),
            $this->parseAmount->parse($infos['amount'], (int) $infos['nb_of_dec'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '01';
    }
}

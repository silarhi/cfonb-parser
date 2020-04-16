<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Banking\Balance;

class Line01Parser extends AbstractCfonb120Parser
{
    public function parse($content)
    {
        $infos = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [self::NUMERIC, 5],
            '_unused_1' => [self::BLANK, 4],
            'desk_code' => [self::NUMERIC, 5],
            'currency_code' => [self::ALPHA_BLANK, 3],
            'nb_of_dec' => [self::NUMERIC_BLANK, 1],
            '_unused_2' => [self::BLANK, 1],
            'account_nb' => [self::ALPHANUMERIC, 11],
            '_unused_3' => [self::BLANK, 2],
            'date' => [self::NUMERIC, 6],
            '_unused_4' => [self::BLANK, 50],
            'amount' => [self::AMOUNT, 14],
            '_unused_5' => [self::ALL, 16],
        ]);

        $balance = new Balance();
        $balance
            ->setBankCode($infos['bank_code'])
            ->setDeskCode($infos['desk_code'])
            ->setCurrencyCode($infos['currency_code'])
            ->setAccountNumber($infos['account_nb'])
            ->setDate($this->parseDate($infos['date']))
            ->setAmount($this->parseAmount($infos['amount'], $infos['nb_of_dec']));

        return $balance;
    }

    public function getSupportedCode()
    {
        return '01';
    }
}

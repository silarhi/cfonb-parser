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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Contracts\Cfonb120\BalanceInterface;
use Silarhi\Cfonb\Models\Cfonb120\NewBalance;
use Silarhi\Cfonb\Parser\LineParser;

class Line07Parser extends LineParser
{
    public function parse(string $content) : BalanceInterface
    {
        $info = $this->parseLine($content, [
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
            '_unused_4' => [self::ALL, 50],
            'amount' => [self::AMOUNT, 14],
            '_unused_5' => [self::ALL, 16],
        ]);

        return new NewBalance(
            $info['bank_code'],
            $info['desk_code'],
            $info['currency_code'],
            $info['account_nb'],
            $this->parseDate($info['date']),
            $this->parseAmount($info['amount'], $info['nb_of_dec'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '07';
    }
}

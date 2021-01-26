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

use Silarhi\Cfonb\Contracts\Cfonb120\OperationInterface;
use Silarhi\Cfonb\Models\Cfonb120\Operation;
use Silarhi\Cfonb\Parser\LineParser;

class Line04Parser extends LineParser
{
    public function parse(string $content) : OperationInterface
    {
        $info = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [self::NUMERIC, 5],
            'internal_code' => [self::ALPHANUMERIC_BLANK, 4],
            'desk_code' => [self::NUMERIC, 5],
            'currency_code' => [self::ALPHA_BLANK, 3],
            'nb_of_dec' => [self::NUMERIC_BLANK, 1],
            '_unused_1' => [self::ALL, 1],
            'account_nb' => [self::ALPHANUMERIC, 11],
            'operation_code' => [self::ALPHANUMERIC, 2],
            'operation_date' => [self::NUMERIC, 6],
            'reject_code' => [self::NUMERIC_BLANK, 2],
            'value_date' => [self::NUMERIC, 6],
            'label' => [self::ALL, 31],
            '_unused_2' => [self::ALL, 2],
            'reference' => [self::ALL, 7],
            'exempt_code' => [self::ALL, 1],
            '_unused_3' => [self::ALL, 1],
            'amount' => [self::AMOUNT, 14],
            '_unused_5' => [self::ALL, 16],
        ]);

        return new Operation(
            $info['bank_code'],
            $info['desk_code'],
            $info['account_nb'],
            $info['operation_code'],
            $this->parseDate($info['operation_date']),
            $this->parseDate($info['value_date']),
            $info['label'],
            $info['reference'],
            $this->parseAmount($info['amount'], $info['nb_of_dec']),
            $info['internal_code'],
            $info['currency_code'],
            $info['reject_code'],
            $info['exempt_code']
        );
    }

    protected function getSupportedCode(): string
    {
        return '04';
    }
}

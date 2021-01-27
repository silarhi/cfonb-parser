<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Parser\Cfonb240;

use Silarhi\Cfonb\Models\Cfonb240\Total;
use Silarhi\Cfonb\Parser\LineParser;

/**
 * A "TOTAL" record.
 * This record contains, in addition to the same information as the "Header" record (operation code, name and bank
 * details of the recipient), the total amount of the transactions appearing in the "DETAIL" records.
 */
class Line39Parser extends LineParser
{
    public function parse(string $content): Total
    {
        $info = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [self::NUMERIC, 6],
            'op_code' => [self::ALL, 2],
            'creation_transaction_file_date' => [self::NUMERIC, 6],
            'discount_cur_idx' => [self::ALPHANUMERIC, 1],
            '_unused_1' => [self::BLANK, 4],
            'recipient_customer_bank_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_customer_counter_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_customer_account_nb_1' => [self::ALPHANUMERIC, 11],
            'recipient_customer_name_1' => [self::ALL, 24],
            '_unused_2' => [self::BLANK, 11],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_customer_bank_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_customer_counter_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_customer_account_nb_2' => [self::ALPHANUMERIC, 11],
            'recipient_customer_name_2' => [self::ALL, 24],
            'processing_center_code' => [self::ALL, 6],
            '_unused_3' => [self::BLANK, 100],
            'total_amount' => [self::NUMERIC, 12],
        ]);

        return new Total(
            (int)$info['seq_nb'],
            $info['op_code'],
            $this->parseDate($info['creation_transaction_file_date']),
            $info['discount_cur_idx'],
            $info['recipient_customer_bank_code_1'],
            $info['recipient_customer_counter_code_1'],
            $info['recipient_customer_account_nb_1'],
            $info['recipient_customer_name_1'],
            $info['recipient_customer_bank_code_2'],
            $info['recipient_customer_counter_code_2'],
            $info['recipient_customer_account_nb_2'],
            $info['recipient_customer_name_2'],
            $info['processing_center_code'],
            $this->parseMoney($info['total_amount'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '39';
    }
}

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

use Silarhi\Cfonb\Models\Cfonb240\Header;
use Silarhi\Cfonb\Parser\LineParser;

/**
 * A "HEADER" record.
 * This record contains the operation code and information (name, bank details) of the recipient.
 */
class Line31Parser extends LineParser
{
    public function parse(string $content): Header
    {
        $info = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [self::NUMERIC, 6],
            'op_code' => [self::ALL, 2],
            'prev_trans_file_date' => [self::NUMERIC, 6],
            'discount_cur_idx' => [self::ALPHANUMERIC, 1],
            '_unused_1' => [self::BLANK, 4],
            'recipient_bank_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_counter_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_account_nb_1' => [self::ALPHANUMERIC, 11],
            'recipient_name_1' => [self::ALL, 24],
            '_unused_2' => [self::BLANK, 11],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_counter_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_account_nb_2' => [self::ALPHANUMERIC, 11],
            'recipient_name_2' => [self::ALL, 24],
            'processing_center_code' => [self::ALL, 6],
            '_unused_3' => [self::ALL, 112],
        ]);

        return new Header(
            (int)$info['seq_nb'],
            $info['op_code'],
            $this->parseDate($info['prev_trans_file_date']),
            $info['discount_cur_idx'],
            $info['recipient_bank_code_1'],
            $info['recipient_counter_code_1'],
            $info['recipient_account_nb_1'],
            $info['recipient_name_1'],
            $info['recipient_bank_code_2'],
            $info['recipient_counter_code_2'],
            $info['recipient_account_nb_2'],
            $info['recipient_name_2'],
            $info['processing_center_code']
        );
    }

    protected function getSupportedCode(): string
    {
        return '31';
    }
}

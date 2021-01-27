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

use Silarhi\Cfonb\Models\Cfonb240\Transaction;
use Silarhi\Cfonb\Parser\LineParser;

/**
 * 0 to n "DETAIL" records.
 * This record contains information specific to each operation.
 */
class Line34Parser extends LineParser
{
    public function parse(string $content): Transaction
    {
        $info = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [self::NUMERIC, 6],
            'op_code' => [self::ALL, 2],
            'settlement_date' => [self::NUMERIC, 6],
            'cur_idx' => [self::ALPHANUMERIC, 1],
            '_unused_1' => [self::ALL, 4],
            'recipient_bank_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_counter_code_1' => [self::ALPHANUMERIC, 5],
            'recipient_account_nb_1' => [self::ALL, 11],
            'recipient_name_1' => [self::ALL, 24],
            'national_issuer_nb' => [self::ALL, 6],
            '_unused_2' => [self::BLANK, 5],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_counter_code_2' => [self::ALPHANUMERIC, 5],
            'recipient_account_nb_2' => [self::ALPHANUMERIC, 11],
            'recipient_name_2' => [self::ALL, 24],
            'presenter_reference' => [self::ALL, 6],
            'description' => [self::ALL, 88],
            'initial_transaction_settlement_date' => [self::ALL, 6],
            'initial_operation_presenter_reference' => [self::ALL, 6],
            'transaction_amount' => [self::NUMERIC, 12],
        ]);

        return new Transaction(
            (int)$info['seq_nb'],
            $info['op_code'],
            $this->parseDate($info['settlement_date']),
            $info['cur_idx'],
            $info['recipient_bank_code_1'],
            $info['recipient_counter_code_1'],
            $info['recipient_account_nb_1'],
            $info['recipient_name_1'],
            $info['national_issuer_nb'],
            $info['recipient_bank_code_2'],
            $info['recipient_counter_code_2'],
            $info['recipient_account_nb_2'],
            $info['recipient_name_2'],
            $info['presenter_reference'],
            $info['description'],
            $info['initial_transaction_settlement_date'] ?
                $this->parseDate($info['initial_transaction_settlement_date']) : null,
            $info['initial_operation_presenter_reference'],
            $this->parseMoney($info['transaction_amount'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '34';
    }
}

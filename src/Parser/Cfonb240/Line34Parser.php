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

namespace Silarhi\Cfonb\Parser\Cfonb240;

use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\MoneyParser;

/** @internal  */
final class Line34Parser extends AbstractCfonb240Parser
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
     * @var MoneyParser
     */
    private $parseAmount;

    public function __construct()
    {
        $this->lineParser = new LineParser();
        $this->parseDate = new DateParser();
        $this->parseAmount = new MoneyParser();
    }

    public function parse(string $content): Transaction
    {
        $info = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [LineParser::NUMERIC, 6],
            'op_code' => [LineParser::ALL, 2],
            'settlement_date' => [LineParser::NUMERIC, 6],
            'cur_idx' => [LineParser::ALPHANUMERIC, 1],
            '_unused_1' => [LineParser::ALL, 4],
            'recipient_bank_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_counter_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_account_nb_1' => [LineParser::ALL, 11],
            'recipient_name_1' => [LineParser::ALL, 24],
            'national_issuer_nb' => [LineParser::ALL, 6],
            '_unused_2' => [LineParser::BLANK, 5],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_counter_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_account_nb_2' => [LineParser::ALPHANUMERIC, 11],
            'recipient_name_2' => [LineParser::ALL, 24],
            'presenter_reference' => [LineParser::ALL, 6],
            'description' => [LineParser::ALL, 88],
            'initial_transaction_settlement_date' => [LineParser::ALL, 6],
            'initial_operation_presenter_reference' => [LineParser::ALL, 6],
            'transaction_amount' => [LineParser::NUMERIC, 12],
        ]);

        return new Transaction(
            (int) $info['seq_nb'],
            $info['op_code'],
            $this->parseDate->parse($info['settlement_date']),
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
                $this->parseDate->parse($info['initial_transaction_settlement_date']) : null,
            $info['initial_operation_presenter_reference'],
            $this->parseAmount->parser($info['transaction_amount'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '34';
    }
}

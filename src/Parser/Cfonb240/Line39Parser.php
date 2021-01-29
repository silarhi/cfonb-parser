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

use Silarhi\Cfonb\Banking\Total;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\MoneyParser;

/** @internal  */
final class Line39Parser extends AbstractCfonb240Parser
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

    public function parse(string $content): Total
    {
        $info = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [LineParser::NUMERIC, 6],
            'op_code' => [LineParser::ALL, 2],
            'creation_transaction_file_date' => [LineParser::NUMERIC, 6],
            'discount_cur_idx' => [LineParser::ALPHANUMERIC, 1],
            '_unused_1' => [LineParser::BLANK, 4],
            'recipient_customer_bank_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_customer_counter_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_customer_account_nb_1' => [LineParser::ALPHANUMERIC, 11],
            'recipient_customer_name_1' => [LineParser::ALL, 24],
            '_unused_2' => [LineParser::BLANK, 11],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_customer_bank_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_customer_counter_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_customer_account_nb_2' => [LineParser::ALPHANUMERIC, 11],
            'recipient_customer_name_2' => [LineParser::ALL, 24],
            'processing_center_code' => [LineParser::ALL, 6],
            '_unused_3' => [LineParser::BLANK, 100],
            'total_amount' => [LineParser::NUMERIC, 12],
        ]);

        return new Total(
            (int) $info['seq_nb'],
            $info['op_code'],
            $this->parseDate->parse($info['creation_transaction_file_date']),
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
            $this->parseAmount->parser($info['total_amount'])
        );
    }

    protected function getSupportedCode(): string
    {
        return '39';
    }
}

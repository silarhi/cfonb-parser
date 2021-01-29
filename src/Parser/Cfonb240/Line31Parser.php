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

use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Parser\AmountParser;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;

/**
 * A "HEADER" record.
 * This record contains the operation code and information (name, bank details) of the recipient.
 */
class Line31Parser extends AbstractCfonb240Parser
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

    public function parse(string $content): Header
    {
        $info = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'seq_nb' => [LineParser::NUMERIC, 6],
            'op_code' => [LineParser::ALL, 2],
            'prev_trans_file_date' => [LineParser::NUMERIC, 6],
            'discount_cur_idx' => [LineParser::ALPHANUMERIC, 1],
            '_unused_1' => [LineParser::BLANK, 4],
            'recipient_bank_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_counter_code_1' => [LineParser::ALPHANUMERIC, 5],
            'recipient_account_nb_1' => [LineParser::ALPHANUMERIC, 11],
            'recipient_name_1' => [LineParser::ALL, 24],
            '_unused_2' => [LineParser::BLANK, 11],
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_counter_code_2' => [LineParser::ALPHANUMERIC, 5],
            'recipient_account_nb_2' => [LineParser::ALPHANUMERIC, 11],
            'recipient_name_2' => [LineParser::ALL, 24],
            'processing_center_code' => [LineParser::ALL, 6],
            '_unused_3' => [LineParser::ALL, 112],
        ]);

        return new Header(
            (int) $info['seq_nb'],
            $info['op_code'],
            $this->parseDate->parse($info['prev_trans_file_date']),
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

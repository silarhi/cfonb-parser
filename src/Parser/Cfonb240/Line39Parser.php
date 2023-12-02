<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) SILARHI <dev@silarhi.fr>
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
use Silarhi\Cfonb\Parser\RegexParts;

/** @internal */
final class Line39Parser extends AbstractCfonb240Parser
{
    private readonly LineParser $lineParser;
    private readonly DateParser $parseDate;
    private readonly MoneyParser $parseAmount;

    public function __construct()
    {
        $this->parseDate = new DateParser();
        $this->parseAmount = new MoneyParser();
        $this->lineParser = new LineParser([
            'record_code' => new RegexParts($this->getSupportedCode(), null, false),
            'seq_nb' => new RegexParts(LineParser::NUMERIC, 6),
            'op_code' => new RegexParts(LineParser::ALL, 2),
            'creation_transaction_file_date' => new RegexParts(LineParser::NUMERIC, 6),
            'discount_cur_idx' => new RegexParts(LineParser::ALPHANUMERIC, 1),
            '_unused_1' => new RegexParts(LineParser::BLANK, 4),
            'recipient_customer_bank_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_customer_counter_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_customer_account_nb_1' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'recipient_customer_name_1' => new RegexParts(LineParser::ALL, 24),
            '_unused_2' => new RegexParts(LineParser::BLANK, 11),
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_customer_bank_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_customer_counter_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_customer_account_nb_2' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'recipient_customer_name_2' => new RegexParts(LineParser::ALL, 24),
            'processing_center_code' => new RegexParts(LineParser::ALL, 6),
            '_unused_3' => new RegexParts(LineParser::BLANK, 100),
            'total_amount' => new RegexParts(LineParser::NUMERIC, 12),
        ]);
    }

    public function parse(string $content, bool $strict): Total
    {
        $regexMatch = $this->lineParser->parse($content);

        return new Total(
            $regexMatch->getInt('seq_nb'),
            $regexMatch->getStringOrNull('op_code'),
            $this->parseDate->parse($regexMatch->getString('creation_transaction_file_date')),
            $regexMatch->getString('discount_cur_idx'),
            $regexMatch->getString('recipient_customer_bank_code_1'),
            $regexMatch->getString('recipient_customer_counter_code_1'),
            $regexMatch->getString('recipient_customer_account_nb_1'),
            $regexMatch->getStringOrNull('recipient_customer_name_1'),
            $regexMatch->getString('recipient_customer_bank_code_2'),
            $regexMatch->getString('recipient_customer_counter_code_2'),
            $regexMatch->getString('recipient_customer_account_nb_2'),
            $regexMatch->getStringOrNull('recipient_customer_name_2'),
            $regexMatch->getStringOrNull('processing_center_code'),
            $this->parseAmount->parser($regexMatch->getFloat('total_amount'))
        );
    }

    protected function getSupportedCode(): string
    {
        return '39';
    }
}

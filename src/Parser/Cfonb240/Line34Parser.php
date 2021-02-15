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
use Silarhi\Cfonb\Parser\RegexParts;

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
        $this->parseDate = new DateParser();
        $this->parseAmount = new MoneyParser();
        $this->lineParser = new LineParser([
            'record_code' => new RegexParts($this->getSupportedCode(), null, false),
            'seq_nb' => new RegexParts(LineParser::NUMERIC, 6),
            'op_code' => new RegexParts(LineParser::ALL, 2),
            'settlement_date' => new RegexParts(LineParser::NUMERIC, 6),
            'cur_idx' => new RegexParts(LineParser::ALPHANUMERIC, 1),
            '_unused_1' => new RegexParts(LineParser::ALL, 4),
            'recipient_bank_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_counter_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_account_nb_1' => new RegexParts(LineParser::ALL, 11),
            'recipient_name_1' => new RegexParts(LineParser::ALL, 24),
            'national_issuer_nb' => new RegexParts(LineParser::ALL, 6),
            '_unused_2' => new RegexParts(LineParser::BLANK, 5),
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_counter_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_account_nb_2' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'recipient_name_2' => new RegexParts(LineParser::ALL, 24),
            'presenter_reference' => new RegexParts(LineParser::ALL, 6),
            'description' => new RegexParts(LineParser::ALL, 88),
            'initial_transaction_settlement_date' => new RegexParts(LineParser::ALL, 6),
            'initial_operation_presenter_reference' => new RegexParts(LineParser::ALL, 6),
            'transaction_amount' => new RegexParts(LineParser::NUMERIC, 12),
        ]);
    }

    public function parse(string $content): Transaction
    {
        $regexMatch = $this->lineParser->parse($content);

        return new Transaction(
            $regexMatch->getInt('seq_nb'),
            $regexMatch->getStringOrNull('op_code'),
            $this->parseDate->parse($regexMatch->getString('settlement_date')),
            $regexMatch->getString('cur_idx'),
            $regexMatch->getString('recipient_bank_code_1'),
            $regexMatch->getString('recipient_counter_code_1'),
            $regexMatch->getStringOrNull('recipient_account_nb_1'),
            $regexMatch->getStringOrNull('recipient_name_1'),
            $regexMatch->getStringOrNull('national_issuer_nb'),
            $regexMatch->getString('recipient_bank_code_2'),
            $regexMatch->getString('recipient_counter_code_2'),
            $regexMatch->getString('recipient_account_nb_2'),
            $regexMatch->getStringOrNull('recipient_name_2'),
            $regexMatch->getStringOrNull('presenter_reference'),
            $regexMatch->getStringOrNull('description'),
            $regexMatch->isNull('initial_transaction_settlement_date') ? null : $this->parseDate->parse($regexMatch->getString('initial_transaction_settlement_date')),
            $regexMatch->getStringOrNull('initial_operation_presenter_reference'),
            $this->parseAmount->parser($regexMatch->getFloat('transaction_amount'))
        );
    }

    protected function getSupportedCode(): string
    {
        return '34';
    }
}

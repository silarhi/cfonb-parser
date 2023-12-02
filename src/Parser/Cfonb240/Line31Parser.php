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

use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\RegexParts;

/** @internal */
final class Line31Parser extends AbstractCfonb240Parser
{
    private readonly LineParser $lineParser;
    private readonly DateParser $parseDate;

    public function __construct()
    {
        $this->parseDate = new DateParser();
        $this->lineParser = new LineParser([
            'record_code' => new RegexParts($this->getSupportedCode(), null, false),
            'seq_nb' => new RegexParts(LineParser::NUMERIC, 6),
            'op_code' => new RegexParts(LineParser::ALL, 2),
            'prev_trans_file_date' => new RegexParts(LineParser::NUMERIC, 6),
            'discount_cur_idx' => new RegexParts(LineParser::ALPHANUMERIC, 1),
            '_unused_1' => new RegexParts(LineParser::BLANK, 4),
            'recipient_bank_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_counter_code_1' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_account_nb_1' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'recipient_name_1' => new RegexParts(LineParser::ALL, 24),
            '_unused_2' => new RegexParts(LineParser::BLANK, 11),
            // This information, when completed, is identical to that appearing in positions 22 to 66 of this record.
            'recipient_bank_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_counter_code_2' => new RegexParts(LineParser::ALPHANUMERIC, 5),
            'recipient_account_nb_2' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'recipient_name_2' => new RegexParts(LineParser::ALL, 24),
            'processing_center_code' => new RegexParts(LineParser::ALL, 6),
            '_unused_3' => new RegexParts(LineParser::ALL, 112),
        ]);
    }

    public function parse(string $content): Header
    {
        $regexMatch = $this->lineParser->parse($content);

        return new Header(
            $regexMatch->getInt('seq_nb'),
            $regexMatch->getStringOrNull('op_code'),
            $this->parseDate->parse($regexMatch->getString('prev_trans_file_date')),
            $regexMatch->getString('discount_cur_idx'),
            $regexMatch->getString('recipient_bank_code_1'),
            $regexMatch->getString('recipient_counter_code_1'),
            $regexMatch->getString('recipient_account_nb_1'),
            $regexMatch->getStringOrNull('recipient_name_1'),
            $regexMatch->getString('recipient_bank_code_2'),
            $regexMatch->getString('recipient_counter_code_2'),
            $regexMatch->getString('recipient_account_nb_2'),
            $regexMatch->getStringOrNull('recipient_name_2'),
            $regexMatch->getStringOrNull('processing_center_code')
        );
    }

    protected function getSupportedCode(): string
    {
        return '31';
    }
}

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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Parser\AmountParser;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\RegexParts;

/** @internal */
final class Line04Parser extends AbstractCfonb120Parser
{
    private readonly LineParser $lineParser;
    private readonly DateParser $parseDate;
    private readonly AmountParser $parseAmount;

    public function __construct()
    {
        $this->parseDate = new DateParser();
        $this->parseAmount = new AmountParser();
        $this->lineParser = new LineParser([
            'record_code' => new RegexParts($this->getSupportedCode(), null, false),
            'bank_code' => new RegexParts(LineParser::NUMERIC, 5),
            'internal_code' => new RegexParts(LineParser::ALPHANUMERIC_BLANK, 4),
            'desk_code' => new RegexParts(LineParser::NUMERIC, 5),
            'currency_code' => new RegexParts(LineParser::ALPHA_BLANK, 3),
            'nb_of_dec' => new RegexParts(LineParser::NUMERIC_BLANK, 1),
            '_unused_1' => new RegexParts(LineParser::ALL, 1),
            'account_nb' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            'operation_code' => new RegexParts(LineParser::ALPHANUMERIC, 2),
            'operation_date' => new RegexParts(LineParser::NUMERIC, 6),
            'reject_code' => new RegexParts(LineParser::NUMERIC_BLANK, 2),
            'value_date' => new RegexParts(LineParser::NUMERIC, 6),
            'label' => new RegexParts(LineParser::ALL, 31),
            '_unused_2' => new RegexParts(LineParser::ALL, 2),
            'reference' => new RegexParts(LineParser::ALL, 7),
            'exempt_code' => new RegexParts(LineParser::ALL, 1),
            '_unused_3' => new RegexParts(LineParser::ALL, 1),
            'amount' => new RegexParts(LineParser::AMOUNT, 13),
            '_unused_5' => new RegexParts(LineParser::ALL, 16),
        ]);
    }

    public function parse(string $content, bool $strict): Operation
    {
        $regexMatch = $this->lineParser->parse($content);

        return new Operation(
            $regexMatch->getString('bank_code'),
            $regexMatch->getString('desk_code'),
            $regexMatch->getString('account_nb'),
            $regexMatch->getString('operation_code'),
            $this->parseDate->parse($regexMatch->getString('operation_date')),
            $this->parseDate->parse($regexMatch->getString('value_date')),
            $regexMatch->getString('label'),
            $regexMatch->getString('reference'),
            $this->parseAmount->parse($regexMatch->getString('amount'), $regexMatch->getInt('nb_of_dec')),
            $regexMatch->getStringOrNull('internal_code'),
            $regexMatch->getStringOrNull('currency_code'),
            $regexMatch->getStringOrNull('reject_code'),
            $regexMatch->getStringOrNull('exempt_code')
        );
    }

    protected function getSupportedCode(): string
    {
        return '04';
    }
}

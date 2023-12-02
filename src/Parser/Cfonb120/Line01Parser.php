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

use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Parser\AmountParser;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\RegexParts;

/** @internal */
class Line01Parser extends AbstractCfonb120Parser
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
            '_unused_1' => new RegexParts(LineParser::ALL, 4),
            'desk_code' => new RegexParts(LineParser::NUMERIC, 5),
            'currency_code' => new RegexParts(LineParser::ALPHA_BLANK, 3),
            'nb_of_dec' => new RegexParts(LineParser::NUMERIC_BLANK, 1),
            '_unused_2' => new RegexParts(LineParser::ALL, 1),
            'account_nb' => new RegexParts(LineParser::ALPHANUMERIC, 11),
            '_unused_3' => new RegexParts(LineParser::ALL, 2),
            'date' => new RegexParts(LineParser::NUMERIC, 6),
            '_unused_4' => new RegexParts(LineParser::ALL, 50),
            'amount' => new RegexParts(LineParser::AMOUNT, 13),
            '_unused_5' => new RegexParts(LineParser::ALL, 16),
        ]);
    }

    public function parse(string $content, bool $strict): Balance
    {
        $regexMatch = $this->lineParser->parse($content);

        return new Balance(
            $regexMatch->getString('bank_code'),
            $regexMatch->getString('desk_code'),
            $regexMatch->getString('currency_code'),
            $regexMatch->getString('account_nb'),
            $this->parseDate->parse($regexMatch->getString('date')),
            $this->parseAmount->parse($regexMatch->getString('amount'), $regexMatch->getInt('nb_of_dec'))
        );
    }

    protected function getSupportedCode(): string
    {
        return '01';
    }
}

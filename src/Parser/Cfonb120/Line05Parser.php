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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Parser\DateParser;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\RegexParts;

/** @internal  */
final class Line05Parser extends AbstractCfonb120Parser
{
    /**
     * @var LineParser
     */
    private $lineParser;
    /**
     * @var DateParser
     */
    private $parseDate;

    public function __construct()
    {
        $this->parseDate = new DateParser();
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
            '_unused_2' => new RegexParts(LineParser::ALL, 5),
            'qualifier' => new RegexParts(LineParser::ALPHANUMERIC, 3),
            'additional_info' => new RegexParts(LineParser::ALL, 70),
            '_unused_3' => new RegexParts(LineParser::ALL, 2),
        ]);
    }

    public function parse(string $content): OperationDetail
    {
        $regexMatch = $this->lineParser->parse($content);

        return new OperationDetail(
            $regexMatch->getString('bank_code'),
            $regexMatch->getString('desk_code'),
            $regexMatch->getString('account_nb'),
            $regexMatch->getString('operation_code'),
            $this->parseDate->parse($regexMatch->getString('operation_date')),
            $regexMatch->getString('qualifier'),
            $regexMatch->getString('additional_info'),
            $regexMatch->getStringOrNull('internal_code'),
            $regexMatch->getStringOrNull('currency_code')
        );
    }

    protected function getSupportedCode(): string
    {
        return '05';
    }
}

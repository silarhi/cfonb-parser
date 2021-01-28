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

class Line05Parser extends AbstractCfonb120Parser
{
    /** @var LineParser */
    private $lineParser;
    /** @var DateParser */
    private $parseDate;

    public function __construct()
    {
        $this->lineParser = new LineParser();
        $this->parseDate  = new DateParser();
    }

    public function parse(string $content): OperationDetail
    {
        $infos = $this->lineParser->parse($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [LineParser::NUMERIC, 5],
            'internal_code' => [LineParser::ALPHANUMERIC_BLANK, 4],
            'desk_code' => [LineParser::NUMERIC, 5],
            'currency_code' => [LineParser::ALPHA_BLANK, 3],
            'nb_of_dec' => [LineParser::NUMERIC_BLANK, 1],
            '_unused_1' => [LineParser::ALL, 1],
            'account_nb' => [LineParser::ALPHANUMERIC, 11],
            'operation_code' => [LineParser::ALPHANUMERIC, 2],
            'operation_date' => [LineParser::NUMERIC, 6],
            '_unused_2' => [LineParser::ALL, 5],
            'qualifier' => [LineParser::ALPHANUMERIC, 3],
            'additional_info' => [LineParser::ALL, 70],
            '_unused_3' => [LineParser::ALL, 2],
        ]);

        return new OperationDetail(
            $infos['bank_code'],
            $infos['desk_code'],
            $infos['account_nb'],
            $infos['operation_code'],
            $this->parseDate->parse($infos['operation_date']),
            $infos['qualifier'],
            $infos['additional_info'],
            $infos['internal_code'],
            $infos['currency_code']
        );
    }

    protected function getSupportedCode(): string
    {
        return '05';
    }
}

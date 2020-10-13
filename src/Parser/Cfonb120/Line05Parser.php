<?php

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

class Line05Parser extends AbstractCfonb120Parser
{
    public function parse(string $content): OperationDetail
    {
        $infos = $this->parseLine($content, [
            'record_code' => '(' . $this->getSupportedCode() . ')',
            'bank_code' => [self::NUMERIC, 5],
            'internal_code' => [self::ALPHANUMERIC_BLANK, 4],
            'desk_code' => [self::NUMERIC, 5],
            'currency_code' => [self::ALPHA_BLANK, 3],
            'nb_of_dec' => [self::NUMERIC_BLANK, 1],
            '_unused_1' => [self::ALL, 1],
            'account_nb' => [self::ALPHANUMERIC, 11],
            'operation_code' => [self::ALPHANUMERIC, 2],
            'operation_date' => [self::NUMERIC, 6],
            '_unused_2' => [self::ALL, 5],
            'qualifier' => [self::ALPHANUMERIC, 3],
            'additional_info' => [self::ALL, 70],
            '_unused_3' => [self::ALL, 2],
        ]);

        return new OperationDetail(
            $infos['bank_code'],
            $infos['desk_code'],
            $infos['account_nb'],
            $infos['operation_code'],
            $this->parseDate($infos['operation_date']),
            $infos['qualifier'],
            $infos['additional_info'],
            $infos['internal_code'],
            $infos['currency_code']
        );
    }

    public function getSupportedCode(): string
    {
        return '05';
    }
}

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
    public function parse($content)
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

        $detail = new OperationDetail();
        $detail
            ->setBankCode($infos['bank_code'])
            ->setInternalCode($infos['internal_code'])
            ->setDeskCode($infos['desk_code'])
            ->setCurrencyCode($infos['currency_code'])
            ->setAccountNumber($infos['account_nb'])
            ->setCode($infos['operation_code'])
            ->setDate($this->parseDate($infos['operation_date']))
            ->setQualifier($infos['qualifier'])
            ->setAdditionalInformations($infos['additional_info'])
        ;

        return $detail;
    }

    public function getSupportedCode()
    {
        return '05';
    }
}

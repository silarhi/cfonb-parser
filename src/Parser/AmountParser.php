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

namespace Silarhi\Cfonb\Parser;

use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;

/** @internal */
final class AmountParser
{
    /** @var string[] */
    private const CREDIT_MAPPING = [
        'A' => '1',
        'B' => '2',
        'C' => '3',
        'D' => '4',
        'E' => '5',
        'F' => '6',
        'G' => '7',
        'H' => '8',
        'I' => '9',
        '{' => '0',
    ];
    /** @var string[] */
    private const DEBIT_MAPPING = [
        'J' => '1',
        'K' => '2',
        'L' => '3',
        'M' => '4',
        'N' => '5',
        'O' => '6',
        'P' => '7',
        'Q' => '8',
        'R' => '9',
        '}' => '0',
    ];

    public function parse(string $content, int $nbDecimals): float
    {
        $lastChar = substr($content, -1);
        if (isset(self::CREDIT_MAPPING[$lastChar])) {
            $sign = 1.0;
            $lastDigit = self::CREDIT_MAPPING[$lastChar];
        } elseif (isset(self::DEBIT_MAPPING[$lastChar])) {
            $sign = -1.0;
            $lastDigit = self::DEBIT_MAPPING[$lastChar];
        } else {
            throw new ParseException(sprintf('Unable to parse amount "%s"', $content));
        }

        $digits = substr($content, 0, -1) . $lastDigit;
        if ($nbDecimals > 0) {
            $digits = substr($digits, 0, -$nbDecimals) . '.' . substr($digits, -$nbDecimals);
        }

        return $sign * (float) $digits;
    }
}

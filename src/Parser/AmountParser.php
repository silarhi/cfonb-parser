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

use Silarhi\Cfonb\Banking\ExactAmount;
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
        [$sign, $digits] = $this->decode($content);
        if ($nbDecimals > 0) {
            $digits = substr($digits, 0, -$nbDecimals) . '.' . substr($digits, -$nbDecimals);
        }

        return (float) $sign * (float) $digits;
    }

    /** The exact amount as a decimal string, "-22.79": unlike the float of {@see parse()}, it never loses a cent. */
    public function parseExact(string $content, int $nbDecimals): string
    {
        [$sign, $digits] = $this->decode($content);

        return ExactAmount::fromMinorUnits($sign * (int) $digits, $nbDecimals);
    }

    /** @return array{int, string} the sign (1 or -1) and the digits, the sign character replaced by its digit */
    private function decode(string $content): array
    {
        $lastChar = substr($content, -1);
        if (isset(self::CREDIT_MAPPING[$lastChar])) {
            return [1, substr($content, 0, -1) . self::CREDIT_MAPPING[$lastChar]];
        }

        if (isset(self::DEBIT_MAPPING[$lastChar])) {
            return [-1, substr($content, 0, -1) . self::DEBIT_MAPPING[$lastChar]];
        }

        // The amount is part of a line: keep it out of the message.
        throw new ParseException(sprintf('Unable to parse amount: its last character "%s" is not a sign character', $lastChar), rawContent: $content);
    }
}

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

namespace Silarhi\Cfonb\Banking;

use InvalidArgumentException;

use function is_string;
use function sprintf;
use function strlen;

/**
 * Exact amounts, the decimal strings of the getters, and their integer arithmetic: "-22.79" is -2279 minor units with 2
 * decimals. Used by the parsers and the objects, and by the validation of statements and transfers, which must not add
 * floats.
 *
 * @internal
 */
final class ExactAmount
{
    private const DECIMAL = '/^-?\d+(?:\.\d+)?$/';

    /** The most decimals of a CFONB 120 amount, position 20 of the line */
    private const MAX_DECIMALS = 9;

    /**
     * @throws InvalidArgumentException when the amount is not a decimal string such as "-22.79"
     */
    public static function check(string $amount): string
    {
        if (1 !== preg_match(self::DECIMAL, $amount)) {
            throw new InvalidArgumentException(sprintf('The exact amount "%s" is not a decimal string such as "-22.79".', $amount));
        }

        return $amount;
    }

    /**
     * The exact amount of an object: a decimal string is checked, a float converted with {@see fromFloat()}.
     *
     * @throws InvalidArgumentException when the amount is a string that is not a decimal, or INF or NAN
     */
    public static function from(float|string $amount): string
    {
        return is_string($amount) ? self::check($amount) : self::fromFloat($amount);
    }

    /**
     * The decimal string of a float amount, for objects built by hand: the fewest decimals, at least 2, that give the
     * float back, so that (float) of the string is the float. 0.29 is "0.29", never "0.28", and 1669.565 stays
     * "1669.565". A float that needs more than 9 decimals, the most a CFONB amount has, is rounded to 9.
     * number_format() ignores the locale and never writes "-0.00".
     *
     * @throws InvalidArgumentException when the amount is INF or NAN, which number_format() writes "inf" and "nan"
     */
    public static function fromFloat(float $amount): string
    {
        if (!is_finite($amount)) {
            throw new InvalidArgumentException(sprintf('The amount %F is not a finite number.', $amount));
        }

        for ($decimals = 2; $decimals < self::MAX_DECIMALS; ++$decimals) {
            $exact = number_format($amount, $decimals, '.', '');
            if ((float) $exact === $amount) {
                return $exact;
            }
        }

        return number_format($amount, self::MAX_DECIMALS, '.', '');
    }

    /** The largest number of decimals of exact amounts: 2 for "12.5" and "-0.25". */
    public static function decimals(string ...$amounts): int
    {
        $decimals = 0;
        foreach ($amounts as $amount) {
            $point = strpos($amount, '.');
            $decimals = max($decimals, false === $point ? 0 : strlen($amount) - $point - 1);
        }

        return $decimals;
    }

    /**
     * An exact amount as an integer with $decimals decimals, at least its own: "-0.25" with 3 decimals is -250.
     *
     * @param string $amount a decimal string, checked by {@see check()}
     */
    public static function toMinorUnits(string $amount, int $decimals): int
    {
        [$integer, $fraction] = explode('.', ltrim($amount, '-'), 2) + [1 => ''];
        $minorUnits = (int) ($integer . str_pad($fraction, $decimals, '0'));

        return str_starts_with($amount, '-') ? -$minorUnits : $minorUnits;
    }

    /** Formats an amount in minor units as a decimal string: -2279 with 2 decimals is "-22.79". */
    public static function fromMinorUnits(int $minorUnits, int $decimals): string
    {
        $digits = str_pad((string) abs($minorUnits), $decimals + 1, '0', \STR_PAD_LEFT);
        if ($decimals > 0) {
            $digits = substr($digits, 0, strlen($digits) - $decimals) . '.' . substr($digits, -$decimals);
        }

        return ($minorUnits < 0 ? '-' : '') . $digits;
    }
}

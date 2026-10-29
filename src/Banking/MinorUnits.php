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

use function strlen;

/**
 * Integer arithmetic on amounts in minor units, for the validation of statements and transfers.
 *
 * @internal
 */
final class MinorUnits
{
    /** Converts an amount in minor units to more decimals: 1250 with 2 decimals is 12500 with 3. */
    public static function scale(int $minorUnits, int $fromDecimals, int $toDecimals): int
    {
        return $minorUnits * 10 ** ($toDecimals - $fromDecimals);
    }

    /** Formats an amount in minor units for a message: -2279 with 2 decimals is "-22.79". */
    public static function format(int $minorUnits, int $decimals): string
    {
        $digits = str_pad((string) abs($minorUnits), $decimals + 1, '0', \STR_PAD_LEFT);
        if ($decimals > 0) {
            $digits = substr($digits, 0, strlen($digits) - $decimals) . '.' . substr($digits, -$decimals);
        }

        return ($minorUnits < 0 ? '-' : '') . $digits;
    }
}

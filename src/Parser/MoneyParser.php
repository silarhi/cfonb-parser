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

/** @internal */
final class MoneyParser
{
    public function parser(float $money): float
    {
        return $money / 100.0;
    }

    /** The exact amount of a field in cents, as a decimal string: "000000171200" is "1712.00". */
    public function parseExact(string $cents): string
    {
        return ExactAmount::fromMinorUnits((int) $cents, 2);
    }
}

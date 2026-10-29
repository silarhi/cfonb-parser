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

namespace Silarhi\Cfonb\Tests\Banking;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\MinorUnits;

final class MinorUnitsTest extends TestCase
{
    public function testScale(): void
    {
        self::assertSame(12500, MinorUnits::scale(1250, 2, 3));
        self::assertSame(-1250, MinorUnits::scale(-1250, 2, 2));
        self::assertSame(1669565, MinorUnits::scale(1669565, 0, 0));
        self::assertSame(1669565000, MinorUnits::scale(1669565, 2, 5));
    }

    /** @return iterable<string, array{int, int, string}> */
    public static function provideFormatCase(): iterable
    {
        yield 'credit' => [1669565, 2, '16695.65'];
        yield 'debit' => [-2279, 2, '-22.79'];
        yield 'less than one' => [29, 2, '0.29'];
        yield 'debit less than one' => [-5, 2, '-0.05'];
        yield 'zero' => [0, 2, '0.00'];
        yield 'no decimals' => [1669565, 0, '1669565'];
        yield 'three decimals' => [-1669565, 3, '-1669.565'];
    }

    #[DataProvider('provideFormatCase')]
    public function testFormat(int $minorUnits, int $decimals, string $expected): void
    {
        self::assertSame($expected, MinorUnits::format($minorUnits, $decimals));
    }
}

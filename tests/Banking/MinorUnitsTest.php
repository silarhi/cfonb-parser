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

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\MinorUnits;

final class MinorUnitsTest extends TestCase
{
    public function testDecimals(): void
    {
        self::assertSame(3, MinorUnits::decimals('12.5', '-0.025', '0.003'));
        self::assertSame(2, MinorUnits::decimals('16695.65', '-12.50'));
        self::assertSame(0, MinorUnits::decimals('1669565', '-0'));
        self::assertSame(0, MinorUnits::decimals());
    }

    public function testToMinorUnits(): void
    {
        self::assertSame(12500, MinorUnits::toMinorUnits('12.5', 3));
        self::assertSame(-25, MinorUnits::toMinorUnits('-0.025', 3));
        self::assertSame(1669565, MinorUnits::toMinorUnits('16695.65', 2));
        self::assertSame(166956500, MinorUnits::toMinorUnits('1669565', 2));
        self::assertSame(0, MinorUnits::toMinorUnits('-0', 0));
    }

    public function testCheckDecimal(): void
    {
        self::assertSame('-22.79', MinorUnits::checkDecimal('-22.79'));
        self::assertSame('1669565', MinorUnits::checkDecimal('1669565'));

        $this->expectException(InvalidArgumentException::class);
        MinorUnits::checkDecimal('1.2e3');
    }

    public function testFromFloat(): void
    {
        self::assertSame('0.29', MinorUnits::fromFloat(0.29));
        self::assertSame('-22.79', MinorUnits::fromFloat(-22.79));
        self::assertSame('0.00', MinorUnits::fromFloat(-0.0));
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

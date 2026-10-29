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
use Silarhi\Cfonb\Banking\ExactAmount;

final class ExactAmountTest extends TestCase
{
    public function testDecimals(): void
    {
        self::assertSame(3, ExactAmount::decimals('12.5', '-0.025', '0.003'));
        self::assertSame(2, ExactAmount::decimals('16695.65', '-12.50'));
        self::assertSame(0, ExactAmount::decimals('1669565', '-0'));
        self::assertSame(0, ExactAmount::decimals());
    }

    public function testToMinorUnits(): void
    {
        self::assertSame(12500, ExactAmount::toMinorUnits('12.5', 3));
        self::assertSame(-25, ExactAmount::toMinorUnits('-0.025', 3));
        self::assertSame(1669565, ExactAmount::toMinorUnits('16695.65', 2));
        self::assertSame(166956500, ExactAmount::toMinorUnits('1669565', 2));
        self::assertSame(0, ExactAmount::toMinorUnits('-0', 0));
    }

    public function testCheck(): void
    {
        self::assertSame('-22.79', ExactAmount::check('-22.79'));
        self::assertSame('1669565', ExactAmount::check('1669565'));

        $this->expectException(InvalidArgumentException::class);
        ExactAmount::check('1.2e3');
    }

    public function testFromFloat(): void
    {
        self::assertSame('0.29', ExactAmount::fromFloat(0.29));
        self::assertSame('-22.79', ExactAmount::fromFloat(-22.79));
        self::assertSame('0.00', ExactAmount::fromFloat(-0.0));
        self::assertSame('-0.001', ExactAmount::fromFloat(-0.001));
        self::assertSame('1669.565', ExactAmount::fromFloat(1669.565));
        self::assertSame('1.005', ExactAmount::fromFloat(1.005));
        self::assertSame('100000000000000000000.00', ExactAmount::fromFloat(1e20));
        // 0.30000000000000004 needs 17 decimals: rounded to 9, the most a CFONB amount has
        self::assertSame('0.300000000', ExactAmount::fromFloat(0.1 + 0.2));
    }

    public function testFrom(): void
    {
        self::assertSame('-22.79', ExactAmount::from('-22.79'));
        self::assertSame('0.29', ExactAmount::from(0.29));

        $this->expectException(InvalidArgumentException::class);
        ExactAmount::from('12,50');
    }

    /** @return iterable<string, array{float}> */
    public static function provideNotFiniteCase(): iterable
    {
        yield 'INF' => [\INF];
        yield '-INF' => [-\INF];
        yield 'NAN' => [\NAN];
    }

    #[DataProvider('provideNotFiniteCase')]
    public function testFromFloatRejectsNotFiniteAmounts(float $amount): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a finite number.');

        ExactAmount::fromFloat($amount);
    }

    /** @return iterable<string, array{int, int, string}> */
    public static function provideFromMinorUnitsCase(): iterable
    {
        yield 'credit' => [1669565, 2, '16695.65'];
        yield 'debit' => [-2279, 2, '-22.79'];
        yield 'less than one' => [29, 2, '0.29'];
        yield 'debit less than one' => [-5, 2, '-0.05'];
        yield 'zero' => [0, 2, '0.00'];
        yield 'no decimals' => [1669565, 0, '1669565'];
        yield 'three decimals' => [-1669565, 3, '-1669.565'];
    }

    #[DataProvider('provideFromMinorUnitsCase')]
    public function testFromMinorUnits(int $minorUnits, int $decimals, string $expected): void
    {
        self::assertSame($expected, ExactAmount::fromMinorUnits($minorUnits, $decimals));
    }
}

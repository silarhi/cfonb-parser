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

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;

final class BalanceTest extends TestCase
{
    public function testGetter(): void
    {
        $date = new DateTimeImmutable();

        $sUT = new Balance('test', 'test2', 'test3', 'test4', $date, 16695.65, 1669565, 2);

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getCurrencyCode());
        self::assertSame('test4', $sUT->getAccountNumber());
        self::assertSame($date, $sUT->getDate());
        self::assertSame(16695.65, $sUT->getAmount());
        self::assertSame(1669565, $sUT->getAmountInMinorUnits());
        self::assertSame(2, $sUT->getNumberOfDecimals());
    }

    public function testMinorUnitsDerivedFromTheAmount(): void
    {
        // Built by hand without minor units: the float is rounded, (int) (0.29 * 100) would be 28
        $sUT = new Balance('test', 'test2', 'test3', 'test4', new DateTimeImmutable(), 0.29);

        self::assertSame(29, $sUT->getAmountInMinorUnits());
        self::assertSame(2, $sUT->getNumberOfDecimals());

        $sUT = new Balance('test', 'test2', 'test3', 'test4', new DateTimeImmutable(), -1669.565, numberOfDecimals: 3);

        self::assertSame(-1669565, $sUT->getAmountInMinorUnits());
        self::assertSame(3, $sUT->getNumberOfDecimals());
    }
}

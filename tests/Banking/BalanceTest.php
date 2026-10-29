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
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;

final class BalanceTest extends TestCase
{
    public function testGetter(): void
    {
        $date = new DateTimeImmutable();

        $sUT = new Balance('test', 'test2', 'test3', 'test4', $date, 16695.65, '16695.65');

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getCurrencyCode());
        self::assertSame('test4', $sUT->getAccountNumber());
        self::assertSame($date, $sUT->getDate());
        self::assertSame(16695.65, $sUT->getAmount());
        self::assertSame('16695.65', $sUT->getAmount(false));
    }

    public function testExactAmountDerivedFromTheAmount(): void
    {
        // Built by hand without the exact amount: the float with 2 decimals, (int) (0.29 * 100) would be 28
        $sUT = new Balance('test', 'test2', 'test3', 'test4', new DateTimeImmutable(), 0.29);

        self::assertSame('0.29', $sUT->getAmount(false));

        $sUT = new Balance('test', 'test2', 'test3', 'test4', new DateTimeImmutable(), -0.0);

        self::assertSame('0.00', $sUT->getAmount(false));
    }

    public function testRejectsAnExactAmountThatIsNotADecimal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The exact amount "12,50" is not a decimal string such as "-22.79".');

        new Balance('test', 'test2', 'test3', 'test4', new DateTimeImmutable(), 12.5, '12,50');
    }
}

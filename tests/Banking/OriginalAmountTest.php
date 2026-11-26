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
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\OriginalAmount;

final class OriginalAmountTest extends TestCase
{
    public function testGetter(): void
    {
        $sUT = new OriginalAmount('USD', '1250.00', '1.0825');

        self::assertSame('USD', $sUT->getCurrencyCode());
        self::assertSame('1250.00', $sUT->getAmount());
        self::assertSame('1.0825', $sUT->getExchangeRate());
        self::assertNull((new OriginalAmount('USD', '1250.00', null))->getExchangeRate());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OriginalAmount('USD', '12,50', null);
    }

    public function testInvalidExchangeRate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OriginalAmount('USD', '12.50', '1,08');
    }
}

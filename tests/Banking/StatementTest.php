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

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;

class StatementTest extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $sUT = new Statement();

        self::assertCount(0, $sUT->getOperations());
        self::assertFalse($sUT->hasNewBalance());
        self::assertFalse($sUT->hasOldBalance());

        $newBalance = $this->createMock(Balance::class);
        $oldBalance = $this->createMock(Balance::class);

        $sUT->setNewBalance($newBalance);
        $sUT->setOldBalance($oldBalance);

        self::assertTrue($sUT->hasNewBalance());
        self::assertTrue($sUT->hasOldBalance());

        self::assertSame($newBalance, $sUT->getNewBalance());
        self::assertSame($oldBalance, $sUT->getOldBalance());
    }

    /** @return void */
    public function testFailGetOldBalance()
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('old balance is null');

        $sUT->getOldBalance();
    }

    /** @return void */
    public function testFailGetNewBalance()
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('new balance is null');

        $sUT->getNewBalance();
    }
}

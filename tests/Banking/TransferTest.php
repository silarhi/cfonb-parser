<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Tests\Banking;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Banking\Total;
use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;

class TransferTest extends TestCase
{
    /** @return void */
    public function testFailOnHeaderNoAvailable()
    {
        $sUT = new Transfer();

        self::expectException(HeaderUnavailableException::class);
        $sUT->getHeader();
    }

    /** @return void */
    public function testFailOnTotalNoAvailable()
    {
        $sUT = new Transfer();

        self::expectException(TotalUnavailableException::class);
        $sUT->getTotal();
    }

    /** @return void */
    public function testOk()
    {
        $total = self::createMock(Total::class);
        $header = self::createMock(Header::class);
        $transaction = self::createMock(Transaction::class);

        $sUT = new Transfer();
        $sUT->setHeader($header);
        $sUT->setTotal($total);

        self::assertSame($total, $sUT->getTotal());
        self::assertSame($header, $sUT->getHeader());
        self::assertCount(0, $sUT->getTransactions());
        $sUT->addTransaction($transaction);
        self::assertCount(1, $sUT->getTransactions());
    }
}

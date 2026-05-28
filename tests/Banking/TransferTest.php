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
use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Banking\Total;
use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;

final class TransferTest extends TestCase
{
    public function testFailOnHeaderNoAvailable(): void
    {
        $sUT = new Transfer();

        $this->expectException(HeaderUnavailableException::class);
        $sUT->getHeader();
    }

    public function testFailOnTotalNoAvailable(): void
    {
        $sUT = new Transfer();

        $this->expectException(TotalUnavailableException::class);
        $sUT->getTotal();
    }

    public function testOk(): void
    {
        $total = $this->createStub(Total::class);
        $header = $this->createStub(Header::class);
        $transaction = $this->createStub(Transaction::class);

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

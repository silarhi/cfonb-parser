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

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Total;

class TotalTest extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $date = new DateTimeImmutable();

        $sUT = new Total(
            10,
            'test2',
            $date,
            'test3',
            'test4',
            'test5',
            'test6',
            'test7',
            'test8',
            'test9',
            'test10',
            'test11',
            'test12',
            25.2
        );

        self::assertSame(10, $sUT->getSequenceNumber());
        self::assertSame('test2', $sUT->getOperationCode());
        self::assertSame($date, $sUT->getCreationTransactionFileDate());
        self::assertSame('test3', $sUT->getCurrencyIndex());
        self::assertSame('test4', $sUT->getRecipientBankCode1());
        self::assertSame('test5', $sUT->getRecipientCounterCode1());
        self::assertSame('test6', $sUT->getRecipientAccountNumber1());
        self::assertSame('test7', $sUT->getRecipientName1());
        self::assertSame('test8', $sUT->getRecipientBankCode2());
        self::assertSame('test9', $sUT->getRecipientCounterCode2());
        self::assertSame('test10', $sUT->getRecipientAccountNumber2());
        self::assertSame('test11', $sUT->getRecipientName2());
        self::assertSame('test12', $sUT->getProcessingCenterCode());
        self::assertSame(25.2, $sUT->getTotalAmount());
    }
}

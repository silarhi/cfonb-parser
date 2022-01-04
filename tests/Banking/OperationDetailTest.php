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
use Silarhi\Cfonb\Banking\OperationDetail;

class OperationDetailTest extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $date = new DateTimeImmutable();

        $sUT = new OperationDetail(
            'test',
            'test2',
            'test3',
            'test4',
            $date,
            'test5',
            'test6',
            'test7',
            'test8'
        );

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getAccountNumber());
        self::assertSame('test4', $sUT->getCode());
        self::assertSame($date, $sUT->getDate());
        self::assertSame('test5', $sUT->getQualifier());
        self::assertSame('test6', $sUT->getAdditionalInformations());
        self::assertSame('test7', $sUT->getInternalCode());
        self::assertSame('test8', $sUT->getCurrencyCode());
    }
}

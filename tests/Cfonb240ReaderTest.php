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

namespace Silarhi\Cfonb\Tests;

use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

class Cfonb240ReaderTest extends CfonbTestCase
{
    public function testEmpty(): void
    {
        self::assertCount(0, (new Cfonb240Reader())->parse(''));
    }

    public function testFailUnknownLine(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        (new Cfonb240Reader())->parse('abc ');
    }

    public function testFailCauseNoHeaderWithTotal(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach a total for operation with internal code 3');
        (new Cfonb240Reader())->parse('3900000320221220E    300661077100020030401                                   300661077100020030401                                                                                                                                  000000063330');
    }

    public function testFailCauseNoHeaderWithTransaction(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach a total for operation with internal code 2');
        (new Cfonb240Reader())->parse('3400000220221220E0066300030302100020116383CHIC WWW WWWWWWW                   300661077100020030401ZZZZ ZZZZ                                             F2012017                                                        221220      000000011760');
    }

    public function testComplexTest(): void
    {
        $transfers = (new Cfonb240Reader())->parse($this->loadFixture('cfonb.240-complex-test.txt', false));

        self::assertCount(2, $transfers);
        self::assertContainsOnlyInstancesOf(Transfer::class, $transfers);

        $firstTransfers = $transfers[0];

        self::assertCount(2, $firstTransfers->getTransactions());
        self::assertSame('E', $firstTransfers->getHeader()->getCurrencyIndex());
        self::assertSame(4652.7, $firstTransfers->getTotal()->getTotalAmount());
    }
}

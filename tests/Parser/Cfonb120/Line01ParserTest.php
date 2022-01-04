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

namespace Siarhi\Cfonb\Tests\Parser\Cfonb120;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Parser\Cfonb120\Line01Parser;

class Line01ParserTest extends TestCase
{
    private function parse(string $content): Balance
    {
        $sUT = new Line01Parser();

        self::assertTrue($sUT->supports($content));

        return $sUT->parse($content);
    }

    /** @return void */
    public function testOkEmptyUnused1()
    {
        $balance = $this->parse('0110278    02204EUR2 00012345603  060420                                                  0000000166956E060420070420    ');

        self::assertSame('2020-04-06', $balance->getDate()->format('Y-m-d'));
        self::assertSame('02204', $balance->getDeskCode());
        self::assertSame('EUR', $balance->getCurrencyCode());
        self::assertSame('10278', $balance->getBankCode());
        self::assertSame('00012345603', $balance->getAccountNumber());
        self::assertSame(16695.65, $balance->getAmount());
    }

    /** @return void */
    public function testOkNotEmptyUnused1()
    {
        $balance = $this->parse('0110278RLV 02204EUR2 00012345603  060420                                                  0000000166956E060420070420    ');

        self::assertSame('2020-04-06', $balance->getDate()->format('Y-m-d'));
        self::assertSame('02204', $balance->getDeskCode());
        self::assertSame('EUR', $balance->getCurrencyCode());
        self::assertSame('10278', $balance->getBankCode());
        self::assertSame('00012345603', $balance->getAccountNumber());
        self::assertSame(16695.65, $balance->getAmount());
    }
}

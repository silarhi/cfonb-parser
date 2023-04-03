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

namespace Silarhi\Cfonb\Tests\Parser\Cfonb240;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Parser\Cfonb240\Line34Parser;

class Line34ParserTest extends TestCase
{
    private function parse(string $content): Transaction
    {
        $sUT = new Line34Parser();

        self::assertTrue($sUT->supports($content));

        return $sUT->parse($content);
    }

    /** @return void */
    public function testOk()
    {
        $balance = $this->parse('3400000220211220E0066149890000122140469732OFFICE NATL DU AAAAAAAA            300661077100020030401ZZZZZZZZ                                                                                                              211220      000000171200');

        self::assertNull($balance->getNationalIssuerNumber());
        self::assertSame(1712.0, $balance->getTransactionAmount());
        self::assertNull($balance->getInitialOperationPresenterReference());
        self::assertNotNull($balance->getInitialTransactionSettlementDate());
        self::assertNull($balance->getDescription());
        self::assertNull($balance->getPresenterReference());
        self::assertSame('E', $balance->getCurIndex());
        self::assertSame('2020-12-21', $balance->getSettlementDate()->format('Y-m-d'));
        self::assertSame('00001', $balance->getRecipientCounterCode1());
        self::assertSame('30066', $balance->getRecipientBankCode2());
        self::assertSame('OFFICE NATL DU AAAAAAAA', $balance->getRecipientName1());
        self::assertSame('22140469732', $balance->getRecipientAccountNumber1());
        self::assertSame('20', $balance->getOperationCode());
    }

    /** @return void */
    public function testEmptyDate()
    {
        $balance = $this->parse('3400000220211220E0066149890000122140469732OFFICE NATL DU AAAAAAAA            300661077100020030401ZZZZZZZZ                                                                                                                          000000171200');

        self::assertNull($balance->getNationalIssuerNumber());
        self::assertSame(1712.0, $balance->getTransactionAmount());
        self::assertNull($balance->getInitialOperationPresenterReference());
        self::assertNull($balance->getInitialTransactionSettlementDate());
        self::assertNull($balance->getDescription());
        self::assertNull($balance->getPresenterReference());
        self::assertSame('E', $balance->getCurIndex());
        self::assertSame('2020-12-21', $balance->getSettlementDate()->format('Y-m-d'));
        self::assertSame('00001', $balance->getRecipientCounterCode1());
        self::assertSame('30066', $balance->getRecipientBankCode2());
        self::assertSame('OFFICE NATL DU AAAAAAAA', $balance->getRecipientName1());
        self::assertSame('22140469732', $balance->getRecipientAccountNumber1());
        self::assertSame('20', $balance->getOperationCode());
    }
}

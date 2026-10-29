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
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

final class Cfonb240ReaderTest extends CfonbTestCase
{
    public function testEmpty(): void
    {
        self::assertCount(0, (new Cfonb240Reader())->parse(''));
    }

    public function testFailUnknownLine(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to find a parser for the line 1: record code "ab", 4 characters');
        (new Cfonb240Reader())->parse('abc ');
    }

    public function testFailCauseNoHeaderWithTotal(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach the total with sequence number 3: missing "31" header record before it');
        (new Cfonb240Reader())->parse('3900000320221220E    300661077100020030401                                   300661077100020030401                                                                                                                                  000000063330');
    }

    public function testFailCauseNoHeaderWithTransaction(): void
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach the transaction with sequence number 2: missing "31" header record before it');
        (new Cfonb240Reader())->parse('3400000220221220E0066300030302100020116383CHIC WWW WWWWWWW                   300661077100020030401ZZZZ ZZZZ                                             F2012017                                                        221220      000000011760');
    }

    public function testFailCauseTransactionAfterTotal(): void
    {
        $lines = explode("\n", trim(self::loadFixture('cfonb.240-complex-test.txt', false), "\n"));
        // Move the second transfer's "31" header after its "34" detail record: the detail now follows the first "39" total.
        [$lines[4], $lines[5]] = [$lines[5], $lines[4]];

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach the transaction with sequence number 2: missing "31" header record before it');
        (new Cfonb240Reader())->parse(implode("\n", $lines));
    }

    public function testFailCauseTotalAfterTotal(): void
    {
        $lines = explode("\n", trim(self::loadFixture('cfonb.240-complex-test.txt', false), "\n"));
        // Drop the second transfer's "31" header and "34" detail: its "39" total now follows the first "39" total.
        unset($lines[4], $lines[5]);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach the total with sequence number 3: missing "31" header record before it');
        (new Cfonb240Reader())->parse(implode("\n", $lines));
    }

    public function testFailCauseHeaderAfterHeader(): void
    {
        $lines = explode("\n", trim(self::loadFixture('cfonb.240-complex-test.txt', false), "\n"));
        // Drop the first transfer's "39" total: the second transfer's "31" header now follows an open transfer.
        unset($lines[3]);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to start a transfer with the "31" header record of sequence number 1: the previous transfer has no "39" total record');
        (new Cfonb240Reader())->parse(implode("\n", $lines));
    }

    public function testFailCauseTransferNeverClosed(): void
    {
        $lines = explode("\n", trim(self::loadFixture('cfonb.240-complex-test.txt', false), "\n"));
        // Drop the second transfer's "39" total, the last line of the file.
        array_pop($lines);

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to close the transfer of the "31" header record of sequence number 1: missing "39" total record at the end of the file');
        (new Cfonb240Reader())->parse(implode("\n", $lines));
    }

    public function testComplexTest(): void
    {
        $transfers = (new Cfonb240Reader())->parse(self::loadFixture('cfonb.240-complex-test.txt', false));

        self::assertCount(2, $transfers);

        // @phpstan-ignore staticMethod.alreadyNarrowedType
        self::assertContainsOnlyInstancesOf(Transfer::class, $transfers);

        $firstTransfers = $transfers[0];

        self::assertCount(2, $firstTransfers->getTransactions());
        self::assertSame('E', $firstTransfers->getHeader()->getCurrencyIndex());
        self::assertSame(4652.7, $firstTransfers->getTotal()->getTotalAmount());
        self::assertSame('4652.70', $firstTransfers->getTotal()->getTotalAmount(false));
        self::assertSame('1712.00', $firstTransfers->getTransactions()[0]->getTransactionAmount(false));
        self::assertSame('2872.80', $firstTransfers->getTransactions()[1]->getTransactionAmount(false));
    }

    public function testValidate(): void
    {
        $lines = explode("\n", trim(self::loadFixture('cfonb.240-complex-test.txt', false), "\n"));

        // The totals of the fixture are not the sum of its transactions
        try {
            (new Cfonb240Reader())->parse(implode("\n", $lines))[0]->validate();
            self::fail('A ValidationException should be thrown');
        } catch (ValidationException $e) {
            self::assertSame(ValidationCheck::Total, $e->getCheck());
            self::assertSame('Invalid transfer of the "31" header record of sequence number 1: the sum of the transactions (4584.80) is not the "39" total (4652.70)', $e->getMessage());
        }

        // 1712.00 + 2872.80 and 117.60
        $lines[3] = substr_replace($lines[3], '000000458480', 228, 12);
        $lines[6] = substr_replace($lines[6], '000000011760', 228, 12);
        foreach ((new Cfonb240Reader())->parse(implode("\n", $lines)) as $transfer) {
            $transfer->validate();
        }
    }
}

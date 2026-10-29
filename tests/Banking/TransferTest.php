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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\ExactAmount;
use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Banking\Total;
use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

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
        $total = self::createStub(Total::class);
        $header = self::createStub(Header::class);
        $transaction = self::createStub(Transaction::class);

        $sUT = new Transfer();
        $sUT->setHeader($header);
        $sUT->setTotal($total);

        self::assertSame($total, $sUT->getTotal());
        self::assertSame($header, $sUT->getHeader());
        self::assertCount(0, $sUT->getTransactions());
        $sUT->addTransaction($transaction);
        self::assertCount(1, $sUT->getTransactions());
    }

    private const ACCOUNT = '00020030401';

    private static function header(): Header
    {
        return new Header(1, null, new DateTimeImmutable('2020-12-18'), 'E', '30066', '10771', self::ACCOUNT, null, '30066', '10771', self::ACCOUNT, null, null);
    }

    private static function transaction(int $sequenceNumber, int $amount, string $currencyIndex = 'E'): Transaction
    {
        return new Transaction($sequenceNumber, null, new DateTimeImmutable('2020-12-21'), $currencyIndex, '00661', '49890', '00012214046', 'OFFICE NATL', null, '30066', '10771', self::ACCOUNT, null, null, null, null, null, ExactAmount::fromMinorUnits($amount, 2));
    }

    private static function total(int $amount, string $bankCode = '30066', string $counterCode = '10771', string $accountNumber = self::ACCOUNT, string $currencyIndex = 'E'): Total
    {
        return new Total(4, null, new DateTimeImmutable('2020-12-21'), $currencyIndex, $bankCode, $counterCode, $accountNumber, null, '30066', '10771', self::ACCOUNT, null, null, ExactAmount::fromMinorUnits($amount, 2));
    }

    private static function transfer(Total $total, Transaction ...$transactions): Transfer
    {
        $transfer = new Transfer();
        $transfer->setHeader(self::header());
        foreach ($transactions as $transaction) {
            $transfer->addTransaction($transaction);
        }
        $transfer->setTotal($total);

        return $transfer;
    }

    public function testValidate(): void
    {
        $this->expectNotToPerformAssertions();

        self::transfer(self::total(0))->validate();
        // 1712.00 + 2872.80 + 0.29
        self::transfer(self::total(458509), self::transaction(2, 171200), self::transaction(3, 287280), self::transaction(4, 29))->validate();
    }

    /** @return iterable<string, array{Transfer, ValidationCheck, string}> */
    public static function provideInvalidCase(): iterable
    {
        yield 'bank code of the total' => [self::transfer(self::total(0, bankCode: '30067')), ValidationCheck::BankCode, 'the bank code of the "39" total is not the one of the "31" header'];
        yield 'counter code of the total' => [self::transfer(self::total(0, counterCode: '10772')), ValidationCheck::DeskCode, 'the counter code of the "39" total is not the one of the "31" header'];
        yield 'account of the total' => [self::transfer(self::total(0, accountNumber: '99999999999')), ValidationCheck::AccountNumber, 'the account number of the "39" total is not the one of the "31" header'];
        yield 'currency of the total' => [self::transfer(self::total(0, currencyIndex: 'F')), ValidationCheck::Currency, 'the currency index of the "39" total is not the one of the "31" header'];
        yield 'currency of a transaction' => [self::transfer(self::total(171200), self::transaction(2, 171200, 'F')), ValidationCheck::Currency, 'the currency index of the transaction 1 (sequence number 2) is not the one of the "31" header'];
        yield 'total not the sum of the transactions' => [self::transfer(self::total(465270), self::transaction(2, 171200), self::transaction(3, 287280)), ValidationCheck::Total, 'the sum of the transactions (4584.80) is not the "39" total (4652.70)'];
        yield 'a cent off' => [self::transfer(self::total(28), self::transaction(2, 29)), ValidationCheck::Total, 'the sum of the transactions (0.29) is not the "39" total (0.28)'];
    }

    #[DataProvider('provideInvalidCase')]
    public function testValidateFails(Transfer $transfer, ValidationCheck $check, string $message): void
    {
        try {
            $transfer->validate();
            self::fail('A ValidationException should be thrown');
        } catch (ValidationException $e) {
            self::assertSame($check, $e->getCheck());
            self::assertSame('Invalid transfer of the "31" header record of sequence number 1: ' . $message, $e->getMessage());
            self::assertStringNotContainsString(self::ACCOUNT, $e->getMessage());
        }
    }

    public function testValidateFailsWithoutTotal(): void
    {
        $sUT = new Transfer();
        $sUT->setHeader(self::header());

        $this->expectException(TotalUnavailableException::class);
        $sUT->validate();
    }
}

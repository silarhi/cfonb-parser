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
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\ExactAmount;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

final class StatementTest extends TestCase
{
    public function testGetter(): void
    {
        $sUT = new Statement();

        self::assertCount(0, $sUT->getOperations());
        self::assertFalse($sUT->hasNewBalance());
        self::assertFalse($sUT->hasOldBalance());

        $newBalance = self::createStub(Balance::class);
        $oldBalance = self::createStub(Balance::class);

        $sUT->setNewBalance($newBalance);
        $sUT->setOldBalance($oldBalance);

        self::assertTrue($sUT->hasNewBalance());
        self::assertTrue($sUT->hasOldBalance());

        self::assertSame($newBalance, $sUT->getNewBalance());
        self::assertSame($oldBalance, $sUT->getOldBalance());
    }

    public function testFailGetOldBalance(): void
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('old balance is null');

        $sUT->getOldBalance();
    }

    public function testFailGetNewBalance(): void
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('new balance is null');

        $sUT->getNewBalance();
    }

    private const ACCOUNT = '00012345603';

    private static function balance(int $amount, string $date = '2020-04-06', string $bankCode = '10278', string $deskCode = '02204', string $accountNumber = self::ACCOUNT, string $currencyCode = 'EUR', int $decimals = 2): Balance
    {
        return new Balance($bankCode, $deskCode, $currencyCode, $accountNumber, new DateTimeImmutable($date), ExactAmount::fromMinorUnits($amount, $decimals));
    }

    private static function operation(int $amount, string $bankCode = '10278', string $deskCode = '02204', string $accountNumber = self::ACCOUNT, ?string $currencyCode = 'EUR', int $decimals = 2): Operation
    {
        $date = new DateTimeImmutable('2020-04-07');

        return new Operation($bankCode, $deskCode, $accountNumber, 'B1', $date, $date, 'PRLV SEPA ONLINE SAS', 'REF', ExactAmount::fromMinorUnits($amount, $decimals), null, $currencyCode, null, null, null);
    }

    private static function detail(string $accountNumber = self::ACCOUNT, ?string $currencyCode = 'EUR'): OperationDetail
    {
        return new OperationDetail('10278', '02204', $accountNumber, 'B1', new DateTimeImmutable('2020-04-07'), 'LIB', 'DEDIBOX 3706114', null, $currencyCode);
    }

    private static function statement(Balance $oldBalance, Balance $newBalance, Operation ...$operations): Statement
    {
        $statement = (new Statement())->setOldBalance($oldBalance)->setNewBalance($newBalance);
        foreach ($operations as $operation) {
            $statement->addOperation($operation);
        }

        return $statement;
    }

    /** @return iterable<string, array{Statement}> */
    public static function provideValidCase(): iterable
    {
        yield 'no operation' => [self::statement(self::balance(1669565), self::balance(1669565, '2020-04-07'))];
        yield 'operations' => [self::statement(self::balance(1669565), self::balance(1667315, '2020-04-07'), self::operation(-2279), self::operation(29), self::operation(0))];
        // Banks write another bank code on some operations, as in the complex-test.txt fixture
        yield 'another bank code on an operation' => [self::statement(self::balance(1669565), self::balance(1667286, '2020-04-07'), self::operation(-2279, bankCode: '10556')->addDetails(self::detail()))];
        yield 'no currency on an operation and a detail' => [self::statement(self::balance(1669565), self::balance(1667286, '2020-04-07'), self::operation(-2279, currencyCode: null)->addDetails(self::detail(currencyCode: null)))];
        // 100.00 + 0.125 = 100.125
        yield 'different numbers of decimals' => [self::statement(self::balance(10000), self::balance(100125, '2020-04-07', decimals: 3), self::operation(125, decimals: 3))];
    }

    #[DataProvider('provideValidCase')]
    public function testValidate(Statement $statement): void
    {
        $this->expectNotToPerformAssertions();

        $statement->validate();
    }

    /** @return iterable<string, array{Statement, ValidationCheck, string}> */
    public static function provideInvalidCase(): iterable
    {
        $old = self::balance(1669565);
        $new = self::balance(1669565, '2020-04-07');

        yield 'bank code of the new balance' => [self::statement($old, self::balance(1669565, bankCode: '10279')), ValidationCheck::BankCode, 'the bank code of the "07" new balance is not the one of the "01" old balance'];
        yield 'desk code of the new balance' => [self::statement($old, self::balance(1669565, deskCode: '02205')), ValidationCheck::DeskCode, 'the desk code of the "07" new balance is not the one of the "01" old balance'];
        yield 'account of the new balance' => [self::statement($old, self::balance(1669565, accountNumber: '99999999999')), ValidationCheck::AccountNumber, 'the account number of the "07" new balance is not the one of the "01" old balance'];
        yield 'currency of the new balance' => [self::statement($old, self::balance(1669565, currencyCode: 'USD')), ValidationCheck::Currency, 'the currency of the "07" new balance is not the one of the "01" old balance'];
        yield 'account of an operation' => [self::statement($old, $new, self::operation(0), self::operation(0, accountNumber: '99999999999')), ValidationCheck::AccountNumber, 'the account number of the operation 2 (code B1, dated 2020-04-07) is not the one of the "01" old balance'];
        yield 'desk code of an operation' => [self::statement($old, $new, self::operation(0, deskCode: '02205')), ValidationCheck::DeskCode, 'the desk code of the operation 1 (code B1, dated 2020-04-07) is not the one of the "01" old balance'];
        yield 'currency of an operation' => [self::statement($old, $new, self::operation(0, currencyCode: 'USD')), ValidationCheck::Currency, 'the currency of the operation 1 (code B1, dated 2020-04-07) is not the one of the "01" old balance'];
        yield 'account of a detail' => [self::statement($old, $new, self::operation(0)->addDetails(self::detail())->addDetails(self::detail('99999999999'))), ValidationCheck::AccountNumber, 'the account number of the detail 2 of the operation 1 (code B1, dated 2020-04-07) is not the one of the "01" old balance'];
        yield 'currency of a detail' => [self::statement($old, $new, self::operation(0)->addDetails(self::detail(currencyCode: 'USD'))), ValidationCheck::Currency, 'the currency of the detail 1 of the operation 1 (code B1, dated 2020-04-07) is not the one of the "01" old balance'];
        yield 'new balance not the old balance plus the operations' => [self::statement($old, self::balance(1669565, '2020-04-07'), self::operation(-2279)), ValidationCheck::Balance, 'the "01" old balance (16695.65) plus the sum of the operations (-22.79) is 16672.86, not the "07" new balance (16695.65)'];
        // A cent lost by a float conversion, e.g. (int) (0.29 * 100), is caught
        yield 'a cent off' => [self::statement(self::balance(0), self::balance(28, '2020-04-07'), self::operation(29)), ValidationCheck::Balance, 'the "01" old balance (0.00) plus the sum of the operations (0.29) is 0.29, not the "07" new balance (0.28)'];
        yield 'different numbers of decimals' => [self::statement(self::balance(10000), self::balance(100120, '2020-04-07', decimals: 3), self::operation(125, decimals: 3)), ValidationCheck::Balance, 'the "01" old balance (100.000) plus the sum of the operations (0.125) is 100.125, not the "07" new balance (100.120)'];
    }

    #[DataProvider('provideInvalidCase')]
    public function testValidateFails(Statement $statement, ValidationCheck $check, string $message): void
    {
        try {
            $statement->validate();
            self::fail('A ValidationException should be thrown');
        } catch (ValidationException $e) {
            self::assertSame($check, $e->getCheck());
            self::assertSame('Invalid statement opened on 2020-04-06: ' . $message, $e->getMessage());
            self::assertStringNotContainsString(self::ACCOUNT, $e->getMessage());
            self::assertNull($e->getRawContent());
        }
    }

    public function testValidateFailsWithoutBalances(): void
    {
        $this->expectException(BalanceUnavailableException::class);

        (new Statement())->setOldBalance(self::balance(0))->validate();
    }
}

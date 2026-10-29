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

namespace Silarhi\Cfonb\Banking;

use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

use function sprintf;

final class Statement extends Element
{
    private ?Balance $oldBalance = null;

    private ?Balance $newBalance = null;

    /** @var Operation[] */
    private array $operations = [];

    public function addOperation(Operation $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function hasOldBalance(): bool
    {
        return null !== $this->oldBalance;
    }

    public function getOldBalance(): Balance
    {
        if (null === $this->oldBalance) {
            throw new BalanceUnavailableException('old balance is null');
        }

        return $this->oldBalance;
    }

    public function setOldBalance(Balance $oldBalance): self
    {
        $this->oldBalance = $oldBalance;

        return $this;
    }

    public function hasNewBalance(): bool
    {
        return null !== $this->newBalance;
    }

    public function getNewBalance(): Balance
    {
        if (null === $this->newBalance) {
            throw new BalanceUnavailableException('new balance is null');
        }

        return $this->newBalance;
    }

    public function setNewBalance(Balance $newBalance): self
    {
        $this->newBalance = $newBalance;

        return $this;
    }

    /**
     * @return Operation[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * Checks that the statement is consistent, which the reader does not: its "07" new balance, its "04" operations
     * and their "05" details belong to the account of its "01" old balance, and the new balance is the old balance
     * plus the operations, added as integers in minor units.
     *
     * The bank code is only compared between the two balances: banks write another bank code on some operations.
     *
     * @throws ValidationException         on the first check that fails, given by {@see ValidationException::getCheck()}
     * @throws BalanceUnavailableException on a statement built by hand without its balances
     */
    public function validate(): void
    {
        $oldBalance = $this->getOldBalance();
        $newBalance = $this->getNewBalance();

        $record = 'the "07" new balance';
        $this->assertSameField(ValidationCheck::BankCode, 'bank code', $oldBalance->getBankCode(), $newBalance->getBankCode(), $record);
        $this->assertSameField(ValidationCheck::DeskCode, 'desk code', $oldBalance->getDeskCode(), $newBalance->getDeskCode(), $record);
        $this->assertSameField(ValidationCheck::AccountNumber, 'account number', $oldBalance->getAccountNumber(), $newBalance->getAccountNumber(), $record);
        $this->assertSameField(ValidationCheck::Currency, 'currency', $oldBalance->getCurrencyCode(), $newBalance->getCurrencyCode(), $record);

        $amounts = [$oldBalance->getAmount(false), $newBalance->getAmount(false)];
        $number = 0;
        foreach ($this->operations as $operation) {
            $record = sprintf('the operation %d (code %s, dated %s)', ++$number, $operation->getCode(), $operation->getDate()->format('Y-m-d'));
            $this->assertSameRecordAccount($oldBalance, $record, $operation->getDeskCode(), $operation->getAccountNumber(), $operation->getCurrencyCode());

            $detailNumber = 0;
            foreach ($operation->getDetails() as $detail) {
                $this->assertSameRecordAccount($oldBalance, sprintf('the detail %d of %s', ++$detailNumber, $record), $detail->getDeskCode(), $detail->getAccountNumber(), $detail->getCurrencyCode());
            }

            $amounts[] = $operation->getAmount(false);
        }

        // Every amount in the same minor units: the balances and the operations can have a different number of decimals.
        $decimals = ExactAmount::decimals(...$amounts);
        $old = ExactAmount::toMinorUnits($oldBalance->getAmount(false), $decimals);
        $new = ExactAmount::toMinorUnits($newBalance->getAmount(false), $decimals);
        $sum = 0;
        foreach ($this->operations as $operation) {
            $sum += ExactAmount::toMinorUnits($operation->getAmount(false), $decimals);
        }

        if ($old + $sum !== $new) {
            throw new ValidationException(ValidationCheck::Balance, sprintf('%s: the "01" old balance (%s) plus the sum of the operations (%s) is %s, not the "07" new balance (%s)', $this->describe(), ExactAmount::fromMinorUnits($old, $decimals), ExactAmount::fromMinorUnits($sum, $decimals), ExactAmount::fromMinorUnits($old + $sum, $decimals), ExactAmount::fromMinorUnits($new, $decimals)));
        }
    }

    private function assertSameRecordAccount(Balance $oldBalance, string $record, string $deskCode, string $accountNumber, ?string $currencyCode): void
    {
        $this->assertSameField(ValidationCheck::DeskCode, 'desk code', $oldBalance->getDeskCode(), $deskCode, $record);
        $this->assertSameField(ValidationCheck::AccountNumber, 'account number', $oldBalance->getAccountNumber(), $accountNumber, $record);
        // The currency of an operation or a detail is optional
        if (null !== $currencyCode) {
            $this->assertSameField(ValidationCheck::Currency, 'currency', $oldBalance->getCurrencyCode(), $currencyCode, $record);
        }
    }

    private function assertSameField(ValidationCheck $check, string $field, string $expected, string $actual, string $record): void
    {
        // The values are kept out of the message: they are account numbers.
        if ($expected !== $actual) {
            throw new ValidationException($check, sprintf('%s: the %s of %s is not the one of the "01" old balance', $this->describe(), $field, $record));
        }
    }

    private function describe(): string
    {
        return sprintf('Invalid statement opened on %s', $this->getOldBalance()->getDate()->format('Y-m-d'));
    }
}

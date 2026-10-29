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

use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

use function sprintf;

final class Transfer extends Element
{
    private ?Header $header = null;

    /**
     * @var Transaction[]
     */
    private array $transactions = [];

    private ?Total $total = null;

    public function setHeader(Header $header): void
    {
        $this->header = $header;
    }

    public function getHeader(): Header
    {
        if (null === $this->header) {
            throw new HeaderUnavailableException();
        }

        return $this->header;
    }

    public function addTransaction(Transaction $operation): void
    {
        $this->transactions[] = $operation;
    }

    /** @return Transaction[] */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function setTotal(Total $total): void
    {
        $this->total = $total;
    }

    public function getTotal(): Total
    {
        if (null === $this->total) {
            throw new TotalUnavailableException();
        }

        return $this->total;
    }

    /**
     * Checks that the transfer is consistent, which the reader does not: its "39" total belongs to the account of its
     * "31" header, its "34" transactions are in the currency of the header, and the total is the sum of the
     * transactions, added as integers in minor units.
     *
     * @throws ValidationException        on the first check that fails, given by {@see ValidationException::getCheck()}
     * @throws HeaderUnavailableException on a transfer built by hand without its header
     * @throws TotalUnavailableException  on a transfer built by hand without its total
     */
    public function validate(): void
    {
        $header = $this->getHeader();
        $total = $this->getTotal();

        $record = 'the "39" total';
        $this->assertSameField(ValidationCheck::BankCode, 'bank code', $header->getRecipientBankCode1(), $total->getRecipientBankCode1(), $record);
        $this->assertSameField(ValidationCheck::DeskCode, 'counter code', $header->getRecipientCounterCode1(), $total->getRecipientCounterCode1(), $record);
        $this->assertSameField(ValidationCheck::AccountNumber, 'account number', $header->getRecipientAccountNumber1(), $total->getRecipientAccountNumber1(), $record);
        $this->assertSameField(ValidationCheck::Currency, 'currency index', $header->getCurrencyIndex(), $total->getCurrencyIndex(), $record);

        $amounts = [];
        $number = 0;
        foreach ($this->transactions as $transaction) {
            $record = sprintf('the transaction %d (sequence number %d)', ++$number, $transaction->getSequenceNumber());
            $this->assertSameField(ValidationCheck::Currency, 'currency index', $header->getCurrencyIndex(), $transaction->getCurIndex(), $record);

            $amounts[] = $transaction->getTransactionAmount(false);
        }

        $decimals = MinorUnits::decimals($total->getTotalAmount(false), ...$amounts);
        $expected = MinorUnits::toMinorUnits($total->getTotalAmount(false), $decimals);
        $sum = 0;
        foreach ($amounts as $amount) {
            $sum += MinorUnits::toMinorUnits($amount, $decimals);
        }
        if ($sum !== $expected) {
            throw new ValidationException(ValidationCheck::Total, sprintf('%s: the sum of the transactions (%s) is not the "39" total (%s)', $this->describe(), MinorUnits::format($sum, $decimals), MinorUnits::format($expected, $decimals)));
        }
    }

    private function assertSameField(ValidationCheck $check, string $field, string $expected, string $actual, string $record): void
    {
        // The values are kept out of the message: they are account numbers.
        if ($expected !== $actual) {
            throw new ValidationException($check, sprintf('%s: the %s of %s is not the one of the "31" header', $this->describe(), $field, $record));
        }
    }

    private function describe(): string
    {
        return sprintf('Invalid transfer of the "31" header record of sequence number %d', $this->getHeader()->getSequenceNumber());
    }
}

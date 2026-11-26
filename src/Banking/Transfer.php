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

use JsonSerializable;
use Override;
use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

use function sprintf;

/**
 * @phpstan-import-type HeaderArray from Header
 * @phpstan-import-type TransactionArray from Transaction
 * @phpstan-import-type TotalArray from Total
 *
 * @phpstan-type TransferArray array{header: HeaderArray, transactions: list<TransactionArray>, total: TotalArray}
 */
final class Transfer extends Element implements JsonSerializable
{
    /** @var list<Transaction> */
    private array $transactions;

    /**
     * @param Transaction[] $transactions
     */
    public function __construct(
        private ?Header $header = null,
        array $transactions = [],
        private ?Total $total = null,
    ) {
        $this->transactions = array_values($transactions);
    }

    /**
     * @deprecated since 6.7, pass the header to the constructor. 7.0 makes the transfer immutable
     */
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

    /**
     * @deprecated since 6.7, pass the transactions to the constructor. 7.0 makes the transfer immutable
     */
    public function addTransaction(Transaction $operation): void
    {
        $this->transactions[] = $operation;
    }

    /** @return list<Transaction> */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @deprecated since 6.7, pass the total to the constructor. 7.0 makes the transfer immutable
     */
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
            $this->assertSameField(ValidationCheck::Currency, 'currency index', $header->getCurrencyIndex(), $transaction->getCurrencyIndex(), $record);

            $amounts[] = $transaction->getAmount(true);
        }

        $decimals = ExactAmount::decimals($total->getAmount(true), ...$amounts);
        $expected = ExactAmount::toMinorUnits($total->getAmount(true), $decimals);
        $sum = 0;
        foreach ($amounts as $amount) {
            $sum += ExactAmount::toMinorUnits($amount, $decimals);
        }
        if ($sum !== $expected) {
            throw new ValidationException(ValidationCheck::Total, sprintf('%s: the sum of the transactions (%s) is not the "39" total (%s)', $this->describe(), ExactAmount::fromMinorUnits($sum, $decimals), ExactAmount::fromMinorUnits($expected, $decimals)));
        }
    }

    /**
     * The transfer as an array of scalars, for JSON: the amounts are the exact decimal strings, the dates are Y-m-d.
     *
     * @return TransferArray
     *
     * @throws HeaderUnavailableException on a transfer built by hand without its header
     * @throws TotalUnavailableException  on a transfer built by hand without its total
     */
    public function toArray(): array
    {
        return [
            'header' => $this->getHeader()->toArray(),
            'transactions' => array_map(static fn (Transaction $transaction): array => $transaction->toArray(), $this->transactions),
            'total' => $this->getTotal()->toArray(),
        ];
    }

    /** @return TransferArray */
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
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

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

use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * @phpstan-type TransactionArray array{sequence_number: int, operation_code: ?string, settlement_date: string, currency_index: string, recipient_bank_code_1: string, recipient_counter_code_1: string, recipient_account_number_1: ?string, recipient_name_1: ?string, national_issuer_number: ?string, recipient_bank_code_2: string, recipient_counter_code_2: string, recipient_account_number_2: string, recipient_name_2: ?string, presenter_reference: ?string, description: ?string, initial_transaction_settlement_date: ?string, initial_operation_presenter_reference: ?string, amount: string}
 */
final class Transaction extends Element implements JsonSerializable
{
    private readonly string $transactionAmount;

    /**
     * @param float|string $transactionAmount the exact amount, a decimal string such as "1712.00", or a float, kept as
     *                                        the decimal string that gives it back
     *
     * @throws InvalidArgumentException when $transactionAmount is a string that is not a decimal, or INF or NAN
     */
    public function __construct(
        private readonly int $sequenceNumber,
        private readonly ?string $operationCode,
        private readonly DateTimeInterface $settlementDate,
        private readonly string $curIndex,
        private readonly string $recipientBankCode1,
        private readonly string $recipientCounterCode1,
        private readonly ?string $recipientAccountNumber1,
        private readonly ?string $recipientName1,
        private readonly ?string $nationalIssuerNumber,
        private readonly string $recipientBankCode2,
        private readonly string $recipientCounterCode2,
        private readonly string $recipientAccountNumber2,
        private readonly ?string $recipientName2,
        private readonly ?string $presenterReference,
        private readonly ?string $description,
        private readonly ?DateTimeInterface $initialTransactionSettlementDate,
        private readonly ?string $initialOperationPresenterReference,
        float|string $transactionAmount,
    ) {
        $this->transactionAmount = ExactAmount::from($transactionAmount);
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function getOperationCode(): ?string
    {
        return $this->operationCode;
    }

    public function getSettlementDate(): DateTimeInterface
    {
        return $this->settlementDate;
    }

    /**
     * @deprecated since 6.7, use {@see getCurrencyIndex()}. 7.0 removes it
     */
    public function getCurIndex(): string
    {
        return $this->curIndex;
    }

    public function getCurrencyIndex(): string
    {
        return $this->curIndex;
    }

    public function getRecipientBankCode1(): string
    {
        return $this->recipientBankCode1;
    }

    public function getRecipientCounterCode1(): string
    {
        return $this->recipientCounterCode1;
    }

    public function getRecipientAccountNumber1(): ?string
    {
        return $this->recipientAccountNumber1;
    }

    public function getRecipientName1(): ?string
    {
        return $this->recipientName1;
    }

    public function getNationalIssuerNumber(): ?string
    {
        return $this->nationalIssuerNumber;
    }

    public function getRecipientBankCode2(): string
    {
        return $this->recipientBankCode2;
    }

    public function getRecipientCounterCode2(): string
    {
        return $this->recipientCounterCode2;
    }

    public function getRecipientAccountNumber2(): string
    {
        return $this->recipientAccountNumber2;
    }

    public function getRecipientName2(): ?string
    {
        return $this->recipientName2;
    }

    public function getPresenterReference(): ?string
    {
        return $this->presenterReference;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getInitialTransactionSettlementDate(): ?DateTimeInterface
    {
        return $this->initialTransactionSettlementDate;
    }

    public function getInitialOperationPresenterReference(): ?string
    {
        return $this->initialOperationPresenterReference;
    }

    /**
     * The amount as a float by default, rounded like any float: pass true for the exact amount as a decimal string,
     * "1712.00", to store in a DECIMAL column or add with bcmath. 7.0 returns the string only.
     *
     * @return ($asString is true ? string : float)
     *
     * @deprecated since 6.7, use {@see getAmount()}. 7.0 removes it
     */
    public function getTransactionAmount(bool $asString = false): float|string
    {
        return $asString ? $this->transactionAmount : (float) $this->transactionAmount;
    }

    /**
     * The amount as a float by default, rounded like any float: pass true for the exact amount as a decimal string,
     * "1712.00", to store in a DECIMAL column or add with bcmath. 7.0 returns the string only.
     *
     * @return ($asString is true ? string : float)
     */
    public function getAmount(bool $asString = false): float|string
    {
        return $asString ? $this->transactionAmount : (float) $this->transactionAmount;
    }

    /**
     * The transaction as an array of scalars, for JSON: the amounts are the exact decimal strings, the dates are Y-m-d.
     *
     * @return TransactionArray
     */
    public function toArray(): array
    {
        return [
            'sequence_number' => $this->sequenceNumber,
            'operation_code' => $this->operationCode,
            'settlement_date' => $this->settlementDate->format('Y-m-d'),
            'currency_index' => $this->curIndex,
            'recipient_bank_code_1' => $this->recipientBankCode1,
            'recipient_counter_code_1' => $this->recipientCounterCode1,
            'recipient_account_number_1' => $this->recipientAccountNumber1,
            'recipient_name_1' => $this->recipientName1,
            'national_issuer_number' => $this->nationalIssuerNumber,
            'recipient_bank_code_2' => $this->recipientBankCode2,
            'recipient_counter_code_2' => $this->recipientCounterCode2,
            'recipient_account_number_2' => $this->recipientAccountNumber2,
            'recipient_name_2' => $this->recipientName2,
            'presenter_reference' => $this->presenterReference,
            'description' => $this->description,
            'initial_transaction_settlement_date' => $this->initialTransactionSettlementDate?->format('Y-m-d'),
            'initial_operation_presenter_reference' => $this->initialOperationPresenterReference,
            'amount' => $this->transactionAmount,
        ];
    }

    /** @return TransactionArray */
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

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
use JsonSerializable;
use Override;

/**
 * @phpstan-type HeaderArray array{sequence_number: int, operation_code: ?string, prev_transaction_file_date: string, currency_index: string, recipient_bank_code_1: string, recipient_counter_code_1: string, recipient_account_number_1: string, recipient_name_1: ?string, recipient_bank_code_2: string, recipient_counter_code_2: string, recipient_account_number_2: string, recipient_name_2: ?string, processing_center_code: ?string}
 */
final class Header extends Element implements JsonSerializable
{
    public function __construct(
        private readonly int $sequenceNumber,
        private readonly ?string $operationCode,
        private readonly DateTimeInterface $prevTransactionFileDate,
        private readonly string $currencyIndex,
        private readonly string $recipientBankCode1,
        private readonly string $recipientCounterCode1,
        private readonly string $recipientAccountNumber1,
        private readonly ?string $recipientName1,
        private readonly string $recipientBankCode2,
        private readonly string $recipientCounterCode2,
        private readonly string $recipientAccountNumber2,
        private readonly ?string $recipientName2,
        private readonly ?string $processingCenterCode,
    ) {
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function getOperationCode(): ?string
    {
        return $this->operationCode;
    }

    public function getPrevTransactionFileDate(): DateTimeInterface
    {
        return $this->prevTransactionFileDate;
    }

    public function getCurrencyIndex(): string
    {
        return $this->currencyIndex;
    }

    public function getRecipientBankCode1(): string
    {
        return $this->recipientBankCode1;
    }

    public function getRecipientCounterCode1(): string
    {
        return $this->recipientCounterCode1;
    }

    public function getRecipientAccountNumber1(): string
    {
        return $this->recipientAccountNumber1;
    }

    public function getRecipientName1(): ?string
    {
        return $this->recipientName1;
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

    public function getProcessingCenterCode(): ?string
    {
        return $this->processingCenterCode;
    }

    /**
     * The header as an array of scalars, for JSON: the amounts are the exact decimal strings, the dates are Y-m-d.
     *
     * @return HeaderArray
     */
    public function toArray(): array
    {
        return [
            'sequence_number' => $this->sequenceNumber,
            'operation_code' => $this->operationCode,
            'prev_transaction_file_date' => $this->prevTransactionFileDate->format('Y-m-d'),
            'currency_index' => $this->currencyIndex,
            'recipient_bank_code_1' => $this->recipientBankCode1,
            'recipient_counter_code_1' => $this->recipientCounterCode1,
            'recipient_account_number_1' => $this->recipientAccountNumber1,
            'recipient_name_1' => $this->recipientName1,
            'recipient_bank_code_2' => $this->recipientBankCode2,
            'recipient_counter_code_2' => $this->recipientCounterCode2,
            'recipient_account_number_2' => $this->recipientAccountNumber2,
            'recipient_name_2' => $this->recipientName2,
            'processing_center_code' => $this->processingCenterCode,
        ];
    }

    /** @return HeaderArray */
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

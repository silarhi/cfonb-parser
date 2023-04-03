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

class Total extends Element
{
    public function __construct(
        private int $sequenceNumber,
        private ?string $operationCode,
        private DateTimeInterface $creationTransactionFileDate,
        private string $currencyIndex,
        private string $recipientBankCode1,
        private string $recipientCounterCode1,
        private string $recipientAccountNumber1,
        private ?string $recipientName1,
        private string $recipientBankCode2,
        private string $recipientCounterCode2,
        private string $recipientAccountNumber2,
        private ?string $recipientName2,
        private ?string $processingCenterCode,
        private float $totalAmount
    ) {
        $this->processingCenterCode = $processingCenterCode;
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function getOperationCode(): ?string
    {
        return $this->operationCode;
    }

    public function getCreationTransactionFileDate(): DateTimeInterface
    {
        return $this->creationTransactionFileDate;
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

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }
}

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

class Transaction extends Element
{
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
        private readonly float $transactionAmount,
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

    public function getSettlementDate(): DateTimeInterface
    {
        return $this->settlementDate;
    }

    public function getCurIndex(): string
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

    public function getTransactionAmount(): float
    {
        return $this->transactionAmount;
    }
}

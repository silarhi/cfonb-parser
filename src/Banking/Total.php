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

final class Total extends Element
{
    private readonly int $totalAmountInMinorUnits;

    /**
     * @param int|null $totalAmountInMinorUnits the amount in cents; when null, e.g. on a total built by hand, the
     *                                          float amount rounded to cents
     */
    public function __construct(
        private readonly int $sequenceNumber,
        private readonly ?string $operationCode,
        private readonly DateTimeInterface $creationTransactionFileDate,
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
        private readonly float $totalAmount,
        ?int $totalAmountInMinorUnits = null,
    ) {
        $this->totalAmountInMinorUnits = $totalAmountInMinorUnits ?? (int) round($totalAmount * 100.0);
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

    /**
     * The exact amount, in cents as in the file: 4652.70 is 465270. Compare it with the sum of
     * {@see Transaction::getTransactionAmountInMinorUnits()} instead of the floats of {@see getTotalAmount()}.
     */
    public function getTotalAmountInMinorUnits(): int
    {
        return $this->totalAmountInMinorUnits;
    }
}

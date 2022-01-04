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

class Total
{
    /**
     * @var int
     */
    private $sequenceNumber;

    /**
     * @var string|null
     */
    private $operationCode;

    /**
     * @var DateTimeInterface
     */
    private $creationTransactionFileDate;

    /**
     * @var string
     */
    private $currencyIndex;

    /**
     * @var string
     */
    private $recipientBankCode1;

    /**
     * @var string
     */
    private $recipientCounterCode1;

    /**
     * @var string
     */
    private $recipientAccountNumber1;

    /**
     * @var string|null
     */
    private $recipientName1;

    /**
     * @var string
     */
    private $recipientBankCode2;

    /**
     * @var string
     */
    private $recipientCounterCode2;

    /**
     * @var string
     */
    private $recipientAccountNumber2;

    /**
     * @var string|null
     */
    private $recipientName2;

    /**
     * @var string|null
     */
    private $processingCenterCode;

    /**
     * @var float
     */
    private $totalAmount;

    public function __construct(
        int $sequenceNumber,
        ?string $operationCode,
        DateTimeInterface $creationTransactionFileDate,
        string $currencyIndex,
        string $recipientBankCode1,
        string $recipientCounterCode1,
        string $recipientAccountNumber1,
        ?string $recipientName1,
        string $recipientBankCode2,
        string $recipientCounterCode2,
        string $recipientAccountNumber2,
        ?string $recipientName2,
        ?string $processingCenterCode,
        float $totalAmount
    ) {
        $this->sequenceNumber = $sequenceNumber;
        $this->operationCode = $operationCode;
        $this->creationTransactionFileDate = $creationTransactionFileDate;
        $this->currencyIndex = $currencyIndex;
        $this->recipientBankCode1 = $recipientBankCode1;
        $this->recipientCounterCode1 = $recipientCounterCode1;
        $this->recipientAccountNumber1 = $recipientAccountNumber1;
        $this->recipientName1 = $recipientName1;
        $this->recipientBankCode2 = $recipientBankCode2;
        $this->recipientCounterCode2 = $recipientCounterCode2;
        $this->recipientAccountNumber2 = $recipientAccountNumber2;
        $this->recipientName2 = $recipientName2;
        $this->processingCenterCode = $processingCenterCode;
        $this->processingCenterCode = $processingCenterCode;
        $this->totalAmount = $totalAmount;
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

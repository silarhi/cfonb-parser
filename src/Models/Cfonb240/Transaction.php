<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Models\Cfonb240;

use DateTimeInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TransactionInterface;

class Transaction implements TransactionInterface
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
    private $settlementDate;

    /**
     * @var string
     */
    private $curIndex;

    /**
     * @var string
     */
    private $recipientBankCode1;

    /**
     * @var string
     */
    private $recipientCounterCode1;

    /**
     * @var string|null
     */
    private $recipientAccountNumber1;

    /**
     * @var string|null
     */
    private $recipientName1;

    /**
     * @var string|null
     */
    private $nationalIssuerNumber;

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
    private $presenterReference;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var DateTimeInterface|null
     */
    private $initialTransactionSettlementDate;

    /**
     * @var string|null
     */
    private $initialOperationPresenterReference;

    /**
     * @var float
     */
    private $transactionAmount;

    public function __construct(
        int $sequenceNumber,
        ?string $operationCode,
        DateTimeInterface $settlementDate,
        string $curIndex,
        string $recipientBankCode1,
        string $recipientCounterCode1,
        ?string $recipientAccountNumber1,
        ?string $recipientName1,
        ?string $nationalIssuerNumber,
        string $recipientBankCode2,
        string $recipientCounterCode2,
        string $recipientAccountNumber2,
        ?string $recipientName2,
        ?string $presenterReference,
        ?string $description,
        ?DateTimeInterface $initialTransactionSettlementDate,
        ?string $initialOperationPresenterReference,
        float $transactionAmount
    ) {
        $this->sequenceNumber = $sequenceNumber;
        $this->operationCode = $operationCode;
        $this->settlementDate = $settlementDate;
        $this->curIndex = $curIndex;
        $this->recipientBankCode1 = $recipientBankCode1;
        $this->recipientCounterCode1 = $recipientCounterCode1;
        $this->recipientAccountNumber1 = $recipientAccountNumber1;
        $this->recipientName1 = $recipientName1;
        $this->nationalIssuerNumber = $nationalIssuerNumber;
        $this->recipientBankCode2 = $recipientBankCode2;
        $this->recipientCounterCode2 = $recipientCounterCode2;
        $this->recipientAccountNumber2 = $recipientAccountNumber2;
        $this->recipientName2 = $recipientName2;
        $this->presenterReference = $presenterReference;
        $this->description = $description;
        $this->initialTransactionSettlementDate = $initialTransactionSettlementDate;
        $this->initialOperationPresenterReference = $initialOperationPresenterReference;
        $this->transactionAmount = $transactionAmount;
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

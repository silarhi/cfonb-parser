<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Banking;

class Operation
{
    /** @var string */
    private $bankCode;

    /** @var string|null */
    private $internalCode;

    /** @var string */
    private $deskCode;

    /** @var string|null */
    private $currencyCode;

    /** @var string */
    private $accountNumber;

    /** @var string */
    private $code;

    /** @var \DateTimeImmutable */
    private $date;

    /** @var string|null */
    private $rejectCode;

    /** @var \DateTimeImmutable */
    private $valueDate;

    /** @var string */
    private $label;

    /** @var string */
    private $reference;

    /** @var string */
    private $exemptCode;

    /** @var float */
    private $amount;

    /** @var OperationDetail|null */
    private $details;

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): self
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getInternalCode(): ?string
    {
        return $this->internalCode;
    }

    public function setInternalCode(?string $internalCode): self
    {
        $this->internalCode = $internalCode;

        return $this;
    }

    public function getDeskCode(): string
    {
        return $this->deskCode;
    }

    public function setDeskCode(string $deskCode): self
    {
        $this->deskCode = $deskCode;

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(?string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRejectCode(): ?string
    {
        return $this->rejectCode;
    }

    public function setRejectCode(?string $rejectCode): self
    {
        $this->rejectCode = $rejectCode;

        return $this;
    }

    public function getValueDate(): \DateTimeImmutable
    {
        return $this->valueDate;
    }

    public function setValueDate(\DateTimeImmutable $valueDate): self
    {
        $this->valueDate = $valueDate;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getExemptCode(): string
    {
        return $this->exemptCode;
    }

    public function setExemptCode(string $exemptCode): self
    {
        $this->exemptCode = $exemptCode;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDetails(): ?OperationDetail
    {
        return $this->details;
    }

    public function setDetails(?OperationDetail $details): self
    {
        $this->details = $details;

        return $this;
    }
}

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

class OperationDetail
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

    /** @var string */
    private $additionalInformations;

    /** @var string */
    private $qualifier;

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

    public function getAdditionalInformations(): string
    {
        return $this->additionalInformations;
    }

    public function setAdditionalInformations(string $additionalInformations): self
    {
        $this->additionalInformations = $additionalInformations;

        return $this;
    }

    public function getQualifier(): string
    {
        return $this->qualifier;
    }

    public function setQualifier(string $qualifier): self
    {
        $this->qualifier = $qualifier;

        return $this;
    }
}

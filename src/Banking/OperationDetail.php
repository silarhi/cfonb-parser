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

    public function __construct(
        string $bankCode,
        string $deskCode,
        string $accountNumber,
        string $code,
        \DateTimeImmutable $date,
        string $qualifier,
        string $additionalInformations,
        ?string $internalCode,
        ?string $currencyCode
    ) {
        $this->bankCode = $bankCode;
        $this->deskCode = $deskCode;
        $this->accountNumber = $accountNumber;
        $this->code = $code;
        $this->date = $date;
        $this->qualifier = $qualifier;
        $this->additionalInformations = $additionalInformations;
        $this->internalCode = $internalCode;
        $this->currencyCode = $currencyCode;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function getInternalCode(): ?string
    {
        return $this->internalCode;
    }

    public function getDeskCode(): string
    {
        return $this->deskCode;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getAdditionalInformations(): string
    {
        return $this->additionalInformations;
    }

    public function getQualifier(): string
    {
        return $this->qualifier;
    }
}

<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Models\Cfonb120;

use DateTimeInterface;
use Silarhi\Cfonb\Contracts\Cfonb120\DetailInterface;

class Detail implements DetailInterface
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

    /** @var DateTimeInterface */
    private $date;

    /** @var string */
    private $additionalInformation;

    /** @var string */
    private $qualifier;

    public function __construct(
        string $bankCode,
        string $deskCode,
        string $accountNumber,
        string $code,
        DateTimeInterface $date,
        string $qualifier,
        string $additionalInformation,
        ?string $internalCode,
        ?string $currencyCode
    ) {
        $this->bankCode = $bankCode;
        $this->deskCode = $deskCode;
        $this->accountNumber = $accountNumber;
        $this->code = $code;
        $this->date = $date;
        $this->qualifier = $qualifier;
        $this->additionalInformation = $additionalInformation;
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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    public function getQualifier(): string
    {
        return $this->qualifier;
    }
}

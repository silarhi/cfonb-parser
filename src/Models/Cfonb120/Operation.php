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
use Silarhi\Cfonb\Contracts\Cfonb120\OperationInterface;

class Operation implements OperationInterface
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

    /** @var string|null */
    private $rejectCode;

    /** @var DateTimeInterface */
    private $valueDate;

    /** @var string */
    private $label;

    /** @var string */
    private $reference;

    /** @var string|null */
    private $exemptCode;

    /** @var float */
    private $amount;

    /** @var DetailInterface[] */
    private $details;

    public function __construct(
        string $bankCode,
        string $deskCode,
        string $accountNumber,
        string $code,
        DateTimeInterface $date,
        DateTimeInterface $valueDate,
        string $label,
        string $reference,
        float $amount,
        ?string $internalCode,
        ?string $currencyCode,
        ?string $rejectCode,
        ?string $exemptCode
    ) {
        $this->bankCode = $bankCode;
        $this->deskCode = $deskCode;
        $this->accountNumber = $accountNumber;
        $this->code = $code;
        $this->date = $date;
        $this->valueDate = $valueDate;
        $this->label = $label;
        $this->reference = $reference;
        $this->amount = $amount;
        $this->internalCode = $internalCode;
        $this->currencyCode = $currencyCode;
        $this->rejectCode = $rejectCode;
        $this->exemptCode = $exemptCode;
        $this->details = [];
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

    public function getRejectCode(): ?string
    {
        return $this->rejectCode;
    }

    public function getValueDate(): DateTimeInterface
    {
        return $this->valueDate;
    }

    public function getLabel(): string
    {
        return $this->label;
    }


    public function getReference(): string
    {
        return $this->reference;
    }

    public function getExemptCode(): ?string
    {
        return $this->exemptCode;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function addDetail(DetailInterface $detail): OperationInterface
    {
        $this->details[] = $detail;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

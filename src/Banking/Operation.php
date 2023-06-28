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

use DateTimeImmutable;

class Operation extends Element
{
    /** @var OperationDetail[] */
    private array $details;

    public function __construct(
        private readonly string $bankCode,
        private readonly string $deskCode,
        private readonly string $accountNumber,
        private readonly string $code,
        private readonly DateTimeImmutable $date,
        private readonly DateTimeImmutable $valueDate,
        private readonly string $label,
        private readonly string $reference,
        private readonly float $amount,
        private readonly ?string $internalCode,
        private readonly ?string $currencyCode,
        private readonly ?string $rejectCode,
        private readonly ?string $exemptCode
    ) {
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

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getRejectCode(): ?string
    {
        return $this->rejectCode;
    }

    public function getValueDate(): DateTimeImmutable
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

    public function addDetails(OperationDetail $details): self
    {
        $this->details[] = $details;

        return $this;
    }

    /** @return OperationDetail[] */
    public function getDetails(): array
    {
        return $this->details;
    }
}

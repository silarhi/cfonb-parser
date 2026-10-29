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

final class Operation extends Element
{
    /** @var OperationDetail[] */
    private array $details = [];

    private readonly int $amountInMinorUnits;

    /**
     * @param int|null $amountInMinorUnits the amount in minor units; when null, e.g. on an operation built by hand,
     *                                     the float amount rounded to $numberOfDecimals
     */
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
        private readonly ?string $exemptCode,
        private readonly ?string $bankOperationReference,
        ?int $amountInMinorUnits = null,
        private readonly int $numberOfDecimals = 2,
    ) {
        $this->amountInMinorUnits = $amountInMinorUnits ?? (int) round($amount * (float) (10 ** $numberOfDecimals));
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

    /**
     * The exact amount, as an integer number of minor units: -22.79 with 2 decimals is -2279. Add and compare these
     * instead of the floats of {@see getAmount()}.
     */
    public function getAmountInMinorUnits(): int
    {
        return $this->amountInMinorUnits;
    }

    /** The number of decimals of the amount (position 20 of the line): 2 for cents. */
    public function getNumberOfDecimals(): int
    {
        return $this->numberOfDecimals;
    }

    public function getBankOperationReference(): ?string
    {
        return $this->bankOperationReference;
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

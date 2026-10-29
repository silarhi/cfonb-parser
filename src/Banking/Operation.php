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
use InvalidArgumentException;

final class Operation extends Element
{
    /** @var OperationDetail[] */
    private array $details = [];

    private readonly string $amount;

    /**
     * @param float|string $amount the exact amount, a decimal string such as "-22.79", or a float, kept as
     *                             the decimal string that gives it back
     *
     * @throws InvalidArgumentException when $amount is a string that is not a decimal, or INF or NAN
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
        float|string $amount,
        private readonly ?string $internalCode,
        private readonly ?string $currencyCode,
        private readonly ?string $rejectCode,
        private readonly ?string $exemptCode,
        private readonly ?string $bankOperationReference,
    ) {
        $this->amount = ExactAmount::from($amount);
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

    /**
     * The amount as a float by default, rounded like any float: pass true for the exact amount as a decimal string,
     * "-22.79", to store in a DECIMAL column or add with bcmath. 7.0 returns the string only.
     *
     * @return ($asString is true ? string : float)
     */
    public function getAmount(bool $asString = false): float|string
    {
        return $asString ? $this->amount : (float) $this->amount;
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

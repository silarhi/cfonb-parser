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
use Silarhi\Cfonb\OperationCode;

final class Operation extends Element
{
    /** @var list<OperationDetail> */
    private array $details = [];

    private readonly string $amount;

    /**
     * @param float|string      $amount  the exact amount, a decimal string such as "-22.79", or a float, kept as
     *                                   the decimal string that gives it back
     * @param OperationDetail[] $details the "05" detail records of the operation, since 6.7
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
        array $details = [],
    ) {
        $this->amount = ExactAmount::from($amount);
        $this->details = array_values($details);
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

    /** The interbank operation code, with its label and direction: null for a code the CFONB does not define */
    public function getOperationCode(): ?OperationCode
    {
        return OperationCode::tryFrom($this->code);
    }

    /** The account of the operation, with its RIB key and IBAN */
    public function getAccount(): Account
    {
        return new Account($this->bankCode, $this->deskCode, $this->accountNumber);
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

    /**
     * The label of the operation followed by the label lines of its "05" detail records of qualifier LIB, separated
     * by a space: the "04" record holds 31 characters of label only.
     */
    public function getFullLabel(): string
    {
        $labels = [$this->label];
        foreach ($this->getDetailsByQualifier(DetailQualifier::Label) as $detail) {
            $label = $detail->getAdditionalInformation();
            if (null !== $label) {
                $labels[] = $label;
            }
        }

        return implode(' ', $labels);
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

    /**
     * @deprecated since 6.7, pass the details to the constructor or use {@see withDetails()}. 7.0 makes the operation
     *             immutable
     */
    public function addDetails(OperationDetail $details): self
    {
        $this->details[] = $details;

        return $this;
    }

    /** A copy of the operation with these "05" detail records instead of its own. */
    public function withDetails(OperationDetail ...$details): self
    {
        $operation = clone $this;
        $operation->details = array_values($details);

        return $operation;
    }

    /** @return list<OperationDetail> */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * The "05" detail records of this qualifier, in order.
     *
     * @return list<OperationDetail>
     */
    public function getDetailsByQualifier(DetailQualifier|string $qualifier): array
    {
        $qualifier = $qualifier instanceof DetailQualifier ? $qualifier->value : $qualifier;

        return array_values(array_filter($this->details, static fn (OperationDetail $detail): bool => $qualifier === $detail->getQualifier()));
    }

    /** The first "05" detail record of this qualifier, null when there is none. */
    public function getDetail(DetailQualifier|string $qualifier): ?OperationDetail
    {
        return $this->getDetailsByQualifier($qualifier)[0] ?? null;
    }

    /** The SEPA information of the operation (payer, beneficiary, references...), decoded from its detail records. */
    public function getSepaInformation(): SepaInformation
    {
        return new SepaInformation(...$this->details);
    }
}

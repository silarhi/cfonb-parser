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

/**
 * The SEPA information of an operation, decoded from its "05" detail records by their qualifier (CFONB, "Evolutions du
 * relevé de compte 120 caractères", version 2.0 of March 2010): the payer, the beneficiary, the remittance information
 * and the references. Each getter is null when the bank gives no detail record of its qualifier; when it gives
 * several, the first one is used.
 */
final readonly class SepaInformation
{
    /** The fields of two-part qualifiers: the first at offset 0, the second at offset 35, of 35 characters each */
    private const HALF = 35;

    /** @var array<string, OperationDetail> the first detail of each qualifier */
    private array $details;

    public function __construct(OperationDetail ...$details)
    {
        $byQualifier = [];
        foreach ($details as $detail) {
            $byQualifier[$detail->getQualifier()] ??= $detail;
        }

        $this->details = $byQualifier;
    }

    public function getPayerName(): ?string
    {
        return $this->field(DetailQualifier::PayerName, 0, 70);
    }

    public function getPayerId(): ?string
    {
        return $this->field(DetailQualifier::PayerId, 0, self::HALF);
    }

    /** The type of the identifier of the payer: "BICOrBEI", or the scheme name of the identifier */
    public function getPayerIdType(): ?string
    {
        return $this->field(DetailQualifier::PayerId, self::HALF, self::HALF);
    }

    public function getBeneficiaryName(): ?string
    {
        return $this->field(DetailQualifier::BeneficiaryName, 0, 70);
    }

    /** The identifier of the beneficiary: for a SEPA direct debit, the SEPA creditor identifier (ICS) */
    public function getBeneficiaryId(): ?string
    {
        return $this->field(DetailQualifier::BeneficiaryId, 0, self::HALF);
    }

    public function getBeneficiaryIdType(): ?string
    {
        return $this->field(DetailQualifier::BeneficiaryId, self::HALF, self::HALF);
    }

    public function getUltimateDebtorName(): ?string
    {
        return $this->field(DetailQualifier::UltimateDebtorName, 0, 70);
    }

    public function getUltimateDebtorId(): ?string
    {
        return $this->field(DetailQualifier::UltimateDebtorId, 0, self::HALF);
    }

    public function getUltimateDebtorIdType(): ?string
    {
        return $this->field(DetailQualifier::UltimateDebtorId, self::HALF, self::HALF);
    }

    public function getUltimateCreditorName(): ?string
    {
        return $this->field(DetailQualifier::UltimateCreditorName, 0, 70);
    }

    public function getUltimateCreditorId(): ?string
    {
        return $this->field(DetailQualifier::UltimateCreditorId, 0, self::HALF);
    }

    public function getUltimateCreditorIdType(): ?string
    {
        return $this->field(DetailQualifier::UltimateCreditorId, self::HALF, self::HALF);
    }

    /**
     * The unstructured remittance information, up to 140 characters: the LCC line, then the LC2 line. The text is cut
     * at the 70th character, in the middle of a word as often as not, so the two lines are joined as they are.
     */
    public function getRemittanceInformation(): ?string
    {
        $line1 = $this->details[DetailQualifier::RemittanceInformation1->value] ?? null;
        $line2 = $this->details[DetailQualifier::RemittanceInformation2->value] ?? null;
        if (null === $line1 && null === $line2) {
            return null;
        }

        // Line 1 keeps its trailing blanks: the word cut at its 70th character goes on in line 2
        $information = trim(($line1?->getRawAdditionalInformation() ?? '') . ($line2?->getRawAdditionalInformation() ?? ''));

        return '' !== $information ? $information : null;
    }

    /** The structured remittance information, the creditor reference such as "RF18539007547034" */
    public function getStructuredRemittanceInformation(): ?string
    {
        return $this->field(DetailQualifier::StructuredRemittanceInformation, 0, self::HALF);
    }

    /** The end-to-end reference, given by the payer from one end of the payment to the other */
    public function getEndToEndReference(): ?string
    {
        return $this->field(DetailQualifier::EndToEndReference, 0, self::HALF);
    }

    /** The purpose of the payment, an ISO 20022 code such as "SALA" */
    public function getPurpose(): ?string
    {
        return $this->field(DetailQualifier::EndToEndReference, self::HALF, self::HALF);
    }

    /** The reference of the remittance, the payment information identification of the ISO 20022 message */
    public function getRemittanceReference(): ?string
    {
        return $this->field(DetailQualifier::RemittanceReference, 0, self::HALF);
    }

    /** The reference of the transaction, the instruction identification of the ISO 20022 message */
    public function getTransactionReference(): ?string
    {
        return $this->field(DetailQualifier::RemittanceReference, self::HALF, self::HALF);
    }

    /**
     * The original amount of a transfer in another currency, null when there is no MMO detail or when its currency or
     * its amount is not filled in as the CFONB defines them.
     */
    public function getOriginalAmount(): ?OriginalAmount
    {
        $detail = $this->details[DetailQualifier::OriginalAmount->value] ?? null;
        if (null === $detail) {
            return null;
        }

        // Positions 49 (3), 52 (1), 53 (14), 67 (2) and 69 (11) of the line
        $currency = $detail->getAdditionalInformationField(0, 3);
        $decimals = $detail->getAdditionalInformationField(3, 1);
        $amount = $detail->getAdditionalInformationField(4, 14);
        if (null === $currency || 1 !== preg_match('/^[A-Z]{3}$/', $currency) || null === $decimals || !ctype_digit($decimals) || null === $amount || !ctype_digit($amount)) {
            return null;
        }

        $rateDecimals = $detail->getAdditionalInformationField(18, 2);
        $rate = $detail->getAdditionalInformationField(20, 11);
        $exchangeRate = null;
        if (null !== $rateDecimals && ctype_digit($rateDecimals) && null !== $rate && ctype_digit($rate) && 0 !== (int) $rate) {
            $exchangeRate = ExactAmount::fromMinorUnits((int) $rate, (int) $rateDecimals);
        }

        return new OriginalAmount($currency, ExactAmount::fromMinorUnits((int) $amount, (int) $decimals), $exchangeRate);
    }

    /** Transfers: the account of the beneficiary, an IBAN as often as not */
    public function getBeneficiaryAccount(): ?string
    {
        return $this->field(DetailQualifier::BeneficiaryAccount, 0, self::HALF);
    }

    /** Direct debits: the unique mandate reference (RUM) */
    public function getMandateReference(): ?string
    {
        return $this->field(DetailQualifier::MandateReference, 0, self::HALF);
    }

    /** Direct debits: the sequence type of the mandate, OOFF, FRST, RCUR or FNAL */
    public function getSequenceType(): ?string
    {
        return $this->field(DetailQualifier::MandateReference, self::HALF, 4);
    }

    /** Direct debits: the account of the debtor */
    public function getDebtorAccount(): ?string
    {
        return $this->field(DetailQualifier::DebtorAccount, 0, self::HALF);
    }

    /**
     * @param int<0, 69> $offset
     * @param int<1, 70> $length
     */
    private function field(DetailQualifier $qualifier, int $offset, int $length): ?string
    {
        return ($this->details[$qualifier->value] ?? null)?->getAdditionalInformationField($offset, $length);
    }
}

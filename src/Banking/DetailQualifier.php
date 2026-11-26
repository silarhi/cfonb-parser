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
 * The qualifier of a CFONB 120 "05" detail record, position 46 of the line: what its additional information holds.
 * LIB is the historical label line, the others come from the SEPA transfers and direct debits (CFONB, "Evolutions du
 * relevé de compte 120 caractères", version 2.0 of March 2010). {@see Operation::getSepaInformation()} decodes them.
 */
enum DetailQualifier: string
{
    /** A label line, after the label of the "04" operation */
    case Label = 'LIB';
    /** The name of the payer (debtor) */
    case PayerName = 'NPY';
    /** The identifier of the payer, then its type */
    case PayerId = 'IPY';
    /** The name of the beneficiary (creditor) */
    case BeneficiaryName = 'NBE';
    /** The identifier of the beneficiary, the SEPA creditor identifier (ICS) of a direct debit, then its type */
    case BeneficiaryId = 'IBE';
    /** The name of the ultimate debtor */
    case UltimateDebtorName = 'NPO';
    /** The identifier of the ultimate debtor, then its type */
    case UltimateDebtorId = 'IPO';
    /** The name of the ultimate creditor */
    case UltimateCreditorName = 'NBU';
    /** The identifier of the ultimate creditor, then its type */
    case UltimateCreditorId = 'IBU';
    /** The first 70 characters of the unstructured remittance information */
    case RemittanceInformation1 = 'LCC';
    /** The last 70 characters of the unstructured remittance information */
    case RemittanceInformation2 = 'LC2';
    /** The structured remittance information (creditor reference) */
    case StructuredRemittanceInformation = 'LCS';
    /** The end-to-end reference, then the purpose of the payment */
    case EndToEndReference = 'RCN';
    /** The reference of the remittance (payment information identification), then of the transaction */
    case RemittanceReference = 'REF';
    /** Transfers: the original currency and amount, and the exchange rate */
    case OriginalAmount = 'MMO';
    /** Transfers: the account of the beneficiary */
    case BeneficiaryAccount = 'CBE';
    /** Direct debits: the unique mandate reference (RUM), then the sequence type */
    case MandateReference = 'RUM';
    /** Direct debits: the account of the debtor */
    case DebtorAccount = 'CPY';

    /** The French label of the CFONB: "Nom du payeur" for NPY. */
    public function label(): string
    {
        return match ($this) {
            self::Label => 'Libellé complémentaire',
            self::PayerName => 'Nom du payeur',
            self::PayerId => 'Identifiant du payeur',
            self::BeneficiaryName => 'Nom du bénéficiaire',
            self::BeneficiaryId => 'Identifiant du bénéficiaire',
            self::UltimateDebtorName => 'Nom du tiers débiteur',
            self::UltimateDebtorId => 'Identifiant du tiers débiteur',
            self::UltimateCreditorName => 'Nom du tiers créancier',
            self::UltimateCreditorId => 'Identifiant du tiers créancier',
            self::RemittanceInformation1 => 'Libellé de client à client, ligne 1',
            self::RemittanceInformation2 => 'Libellé de client à client, ligne 2',
            self::StructuredRemittanceInformation => 'Libellé de client à client structuré',
            self::EndToEndReference => 'Référence de client à client',
            self::RemittanceReference => 'Référence de la remise',
            self::OriginalAmount => "Monnaie et montant d'origine",
            self::BeneficiaryAccount => 'Compte du bénéficiaire',
            self::MandateReference => 'Référence unique du mandat',
            self::DebtorAccount => 'Compte du débiteur',
        };
    }
}

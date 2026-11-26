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

namespace Silarhi\Cfonb;

/**
 * The interbank operation codes of the CFONB 120 "04" and "05" records, position 33 of the line: "05" is a transfer
 * received. {@see Banking\Operation::getOperationCode()} gives the code of an operation.
 *
 * The labels are the French labels of the CFONB, the SWIFT codes and labels their SWIFT (MT940) equivalents.
 */
enum OperationCode: string
{
    case ChequesPaid = '01';
    case ChequeRemittances = '02';
    case UnpaidCheques = '03';
    case CashDeposits = '04';
    case TransfersReceived = '05';
    case TransfersIssued = '06';
    case DomiciledBillsOfExchange = '07';
    case DomiciledDirectDebitsAndTips = '08';
    case IssuedDirectDebitsAndTips = '09';
    case RejectedDirectDebitsAndTips = '10';
    case CardInvoicesAndAtm = '11';
    case ReturnedTransfers = '12';
    case TreasuryTransfersReceived = '13';
    case TreasuryTransfersIssued = '14';
    case CounterChequeRemittances = '15';
    case OtherTransfersReceived = '18';
    case OtherTransfersIssued = '21';
    case DomiciledTips = '22';
    case IssuedTips = '24';
    case RejectedTips = '26';
    case RejectedDirectDebits = '27';
    case CardInvoicesPaid = '28';
    case AtmWithdrawals = '29';
    case CardInvoicesRemitted = '30';
    case BillsRemittedForCollection = '31';
    case BillsRemittedForDiscount = '32';
    case UnpaidBills = '33';
    case BillIncidents = '34';
    case BillsOfExchangeRemittedForCollection = '35';
    case CardCreditInvoiceRemittances = '36';
    case BillsOfExchangeRemittedForDiscount = '37';
    case UnpaidCardInvoices = '38';
    case ExternalOriginTransfersReceived = '39';
    case ForeignTransfers = '41';
    case CurrencyPurchasesAndSales = '42';
    case OtherForeignOperations = '43';
    case InternationalTransfersIssued = '44';
    case InternationalTransfersReceived = '45';
    case SpotCurrencyPurchases = '46';
    case SpotCurrencySales = '47';
    case ForwardPurchaseSettlements = '48';
    case ForwardSaleDeliveries = '49';
    case SecuritiesPurchasesAndSales = '51';
    case MiscellaneousSecuritiesOperations = '52';
    case SecuritiesPurchasesAndSubscriptions = '53';
    case SecuritiesSales = '54';
    case LiquidationBalances = '55';
    case FundPurchases = '56';
    case FundSales = '57';
    case TcnPurchases = '58';
    case TcnSales = '59';
    case Agios = '61';
    case FeesAndCommissions = '62';
    case FinancialIncome = '63';
    case CommissionsIncludingTaxes = '64';
    case CommissionsExcludingTaxes = '65';
    case NonTaxableCommissions = '66';
    case Taxes = '67';
    case OtherForeignOperationsDebit = '68';
    case OtherForeignOperationsCredit = '69';
    case FinancialCharges = '70';
    case LoanDrawdowns = '71';
    case LoanInstalments = '72';
    case SecuritiesIssues = '73';
    case DebtSecuritiesIssues = '74';
    case Repayments = '75';
    case TermDeposits = '76';
    case TermDepositMaturities = '77';
    case OtherTcPurchases = '80';
    case OtherTcSales = '81';
    case Contracts = '82';
    case Options = '83';
    case ForeignStockExchangePurchases = '84';
    case ForeignStockExchangeSales = '85';
    case SecuritiesOperations = '86';
    case SecuritiesIncome = '87';
    case CustodyFees = '88';
    case ForeignTaxRefunds = '89';
    case AcceptedInvoicesAtMaturity = '90';
    case MiscellaneousOperations = '91';
    case ReceiptsCentralisation = '92';
    case ExpensesCentralisation = '93';
    case TreasuryCentralisationCredit = '94';
    case TreasuryCentralisationDebit = '95';
    case DomiciledTelepayments = '96';
    case IssuedTelepayments = '97';
    case RejectedTelepayments = '98';
    case CancellationsAndAdjustments = '99';
    case SepaDirectDebitsIssued = 'A1';
    case SepaB2bDirectDebitsIssued = 'A2';
    case SepaDirectDebitRejectsIssued = 'A3';
    case SepaB2bDirectDebitRejectsIssued = 'A4';
    case SepaDirectDebitRefundsIssued = 'A5';
    case SepaB2bDirectDebitRefundsIssued = 'A6';
    case SepaDirectDebitsDomiciled = 'B1';
    case SepaB2bDirectDebitsDomiciled = 'B2';
    case SepaDirectDebitRejectsReceived = 'B3';
    case SepaB2bDirectDebitRejectsReceived = 'B4';
    case SepaDirectDebitRefundsReceived = 'B5';
    case SepaB2bDirectDebitRefundsReceived = 'B6';
    case SepaInstantTransfersIssued = 'C1';
    case SepaInstantTransfersReceived = 'C2';
    case SepaInstantTransferRecallCredits = 'C3';
    case SepaInstantTransferRecallDebits = 'C4';
    case OriginatorRecredits = 'C5';

    /** The label, the direction, the SWIFT code and the SWIFT label of each code */
    private const DATA = [
        '01' => ['label' => 'Chèques payés', 'direction' => 'D', 'swift_code' => 'CHK', 'swift_label' => 'Cheques'],
        '02' => ['label' => 'Remises de chèques', 'direction' => 'C', 'swift_code' => 'CLR', 'swift_label' => 'Cash letters/Cheques remittance'],
        '03' => ['label' => 'Chèques impayés', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '04' => ['label' => 'Versements espèces', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '05' => ['label' => 'Virements reçus', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '06' => ['label' => 'Virements émis', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '07' => ['label' => 'LCR/BOR domiciliés', 'direction' => 'D', 'swift_code' => 'BOE', 'swift_label' => 'Bill of exchange'],
        '08' => ['label' => 'Prélèvements, TIP et Télérèglements domiciliés', 'direction' => 'D', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '09' => ['label' => 'Prélèvements, TIP et Télérèglements émis', 'direction' => 'C', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '10' => ['label' => 'Prélèvements, TIP et Télérèglements rejetés/impayés', 'direction' => 'D/C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '11' => ['label' => 'Factures cartes de paiement/DAB-GAB', 'direction' => 'C/D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '12' => ['label' => 'Rejet/retour de virement', 'direction' => 'C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '13' => ['label' => 'Virements de trésorerie reçus', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '14' => ['label' => 'Virements de trésorerie émis', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '15' => ['label' => 'Remises de chèques sur caisse', 'direction' => 'C', 'swift_code' => 'CLR', 'swift_label' => 'Cash letters/Cheques remittance'],
        '18' => ['label' => 'Autres virements reçus', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '21' => ['label' => 'Autres virements émis', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '22' => ['label' => 'TIP domiciliés', 'direction' => 'D', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '24' => ['label' => 'TIP émis', 'direction' => 'C', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '26' => ['label' => 'TIP rejetés / impayés', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '27' => ['label' => 'Prélèvements rejetés / impayés', 'direction' => 'D/C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '28' => ['label' => 'Factures cartes payées', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '29' => ['label' => 'Retrait DAB-GAB', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '30' => ['label' => 'Factures cartes remises', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '31' => ['label' => 'Remises d\'effets à l\'encaissement', 'direction' => 'C', 'swift_code' => 'BOE', 'swift_label' => 'Bill of exchange'],
        '32' => ['label' => 'Remises à l\'escompte (papier commercial / chèques)', 'direction' => 'C', 'swift_code' => 'BOE', 'swift_label' => 'Bill of exchange'],
        '33' => ['label' => 'Effets impayés', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '34' => ['label' => 'Incidents sur effets', 'direction' => 'C/D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '35' => ['label' => 'Remises de LCR/BOR à l\'encaissement', 'direction' => 'C', 'swift_code' => 'BOE', 'swift_label' => 'Bill of exchange'],
        '36' => ['label' => 'Remise de facture CB crédit', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '37' => ['label' => 'Remises de LCR/BOR à l\'escompte', 'direction' => 'C', 'swift_code' => 'BOE', 'swift_label' => 'Bill of exchange'],
        '38' => ['label' => 'Facture CB impayée / rejetée', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '39' => ['label' => 'Virements d\'Origine Extérieur (VOE) reçus', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '41' => ['label' => 'Transferts vers/en provenance de l\'étranger', 'direction' => 'D/C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '42' => ['label' => 'Achats/Ventes de devises', 'direction' => 'D/C', 'swift_code' => 'FEX', 'swift_label' => 'Foreign exchange'],
        '43' => ['label' => 'Autres opérations avec l\'étranger', 'direction' => 'D/C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '44' => ['label' => 'Virements internationaux émis', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '45' => ['label' => 'Virements internationaux reçus', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '46' => ['label' => 'Achats de devises au comptant', 'direction' => 'D', 'swift_code' => 'FEX', 'swift_label' => 'Foreign exchange'],
        '47' => ['label' => 'Ventes de devises au comptant', 'direction' => 'C', 'swift_code' => 'FEX', 'swift_label' => 'Foreign exchange'],
        '48' => ['label' => 'Levée sur achat à terme', 'direction' => 'D', 'swift_code' => 'FEX', 'swift_label' => 'Foreign exchange'],
        '49' => ['label' => 'Livraisons sur vente à terme', 'direction' => 'C', 'swift_code' => 'FEX', 'swift_label' => 'Foreign exchange'],
        '51' => ['label' => 'Achats et Ventes de titres', 'direction' => 'D/C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '52' => ['label' => 'Diverses opérations sur titres', 'direction' => 'D/C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '53' => ['label' => 'Achats/Souscriptions VM/BF', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '54' => ['label' => 'Ventes de VM/BF', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '55' => ['label' => 'Solde de liquidation - BF', 'direction' => 'C/D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '56' => ['label' => 'Achats OPCVM', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '57' => ['label' => 'Ventes OPCVM', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '58' => ['label' => 'Achats TCN', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '59' => ['label' => 'Ventes TCN', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '61' => ['label' => 'Agios', 'direction' => 'D/C', 'swift_code' => 'INT', 'swift_label' => 'Interest'],
        '62' => ['label' => 'Commissions et frais divers, dont intérêts compensatoires', 'direction' => 'D', 'swift_code' => 'COM', 'swift_label' => 'Commission'],
        '63' => ['label' => 'Produits financiers', 'direction' => 'C', 'swift_code' => 'INT', 'swift_label' => 'Interest'],
        '64' => ['label' => 'Commissions y compris taxes', 'direction' => 'D', 'swift_code' => 'COM', 'swift_label' => 'Commission'],
        '65' => ['label' => 'Commissions hors taxes', 'direction' => 'D', 'swift_code' => 'COM', 'swift_label' => 'Commission'],
        '66' => ['label' => 'Commissions non taxables', 'direction' => 'D', 'swift_code' => 'COM', 'swift_label' => 'Commission'],
        '67' => ['label' => 'Taxes', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '68' => ['label' => 'Autres opérations avec l\'étranger - Débit', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '69' => ['label' => 'Autres opérations avec l\'étranger - Crédit', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '70' => ['label' => 'Charges financières', 'direction' => 'D', 'swift_code' => 'INT', 'swift_label' => 'Interets'],
        '71' => ['label' => 'Tirage de crédits', 'direction' => 'C', 'swift_code' => 'LDP', 'swift_label' => 'Loan deposit'],
        '72' => ['label' => 'Echéance crédits', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '73' => ['label' => 'Emission de valeurs mobilières', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '74' => ['label' => 'Emission de titres de créances', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '75' => ['label' => 'Remboursement', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '76' => ['label' => 'Dépôt à terme', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '77' => ['label' => 'Echéance dépôt à terme', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '80' => ['label' => 'Achats autres TC', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '81' => ['label' => 'Ventes autres TC', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '82' => ['label' => 'Contrats', 'direction' => 'C/D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '83' => ['label' => 'Options', 'direction' => 'C/D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '84' => ['label' => 'Achats Bourses Etrangères', 'direction' => 'D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '85' => ['label' => 'Ventes Bourses Etrangères', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '86' => ['label' => 'Opérations sur titres', 'direction' => 'C/D', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '87' => ['label' => 'Revenus sur titres', 'direction' => 'C', 'swift_code' => 'SEC', 'swift_label' => 'Securities (used when entering a principal amount)'],
        '88' => ['label' => 'Commissions et droits de garde', 'direction' => 'D', 'swift_code' => 'COM', 'swift_label' => 'Commission'],
        '89' => ['label' => 'Remboursement impôts étrangers', 'direction' => 'C', 'swift_code' => 'REC', 'swift_label' => 'Tax reclaim'],
        '90' => ['label' => 'Facture Acceptée à Echéance émise', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        '91' => ['label' => 'Opérations diverses', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        '92' => ['label' => 'Centralisation de recettes', 'direction' => 'C/D', 'swift_code' => 'CMI', 'swift_label' => 'Cash Management item - No detail'],
        '93' => ['label' => 'Centralisation de dépenses', 'direction' => 'C/D', 'swift_code' => 'CMI', 'swift_label' => 'Cash Management item - No detail'],
        '94' => ['label' => 'Centralisation de trésorerie - Crédit', 'direction' => 'C', 'swift_code' => 'CMI', 'swift_label' => 'Cash Management item - No detail'],
        '95' => ['label' => 'Centralisation de trésorerie - Débit', 'direction' => 'D', 'swift_code' => 'CMI', 'swift_label' => 'Cash Management item - No detail'],
        '96' => ['label' => 'Télérèglements domiciliés', 'direction' => 'D', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '97' => ['label' => 'Télérèglements émis', 'direction' => 'C', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        '98' => ['label' => 'Télérèglements rejetés/impayés', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        '99' => ['label' => 'Annulations et régularisations', 'direction' => 'C/D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'A1' => ['label' => 'Prélèvements SEPA émis', 'direction' => 'C', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        'A2' => ['label' => 'Prélèvements SEPA interentreprises émis', 'direction' => 'C', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        'A3' => ['label' => 'Prélèvements SEPA rejetés/impayés (émis par le débiteur ou sa banque)', 'direction' => 'C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        'A4' => ['label' => 'Prélèvements SEPA interentreprises rejetés/impayés (émis par le débiteur ou sa banque)', 'direction' => 'C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        'A5' => ['label' => 'Prélèvements SEPA – reversements émis (par le créancier ou sa banque)', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'A6' => ['label' => 'Prélèvements SEPA interentreprises – reversements émis (par le créancier ou sa banque)', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'B1' => ['label' => 'Prélèvements SEPA domiciliés', 'direction' => 'D', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        'B2' => ['label' => 'Prélèvements SEPA interentreprises domiciliés', 'direction' => 'D', 'swift_code' => 'DDT', 'swift_label' => 'Direct Debit Item'],
        'B3' => ['label' => 'Prélèvements SEPA rejetés/impayés reçus (par le créancier)', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        'B4' => ['label' => 'Prélèvements SEPA interentreprises rejetés/impayés reçus (par le créancier)', 'direction' => 'D', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        'B5' => ['label' => 'Prélèvements SEPA – reversements reçus (par le débiteur)', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'B6' => ['label' => 'Prélèvements SEPA interentreprises – reversements reçus (par le débiteur)', 'direction' => 'C', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'C1' => ['label' => 'Virement SEPA instantané émis', 'direction' => 'D', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        'C2' => ['label' => 'Virement SEPA instantané reçu', 'direction' => 'C', 'swift_code' => 'TRF', 'swift_label' => 'Transfer'],
        'C3' => ['label' => 'Crédit suite à demande de retour de fonds (« recall ») d’un virement SEPA instantané émise (par le donneur d’ordre)', 'direction' => 'C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
        'C4' => ['label' => 'Débit suite à demande de retour de fonds (« recall ») d’un virement SEPA instantané reçue (par le bénéficiaire)', 'direction' => 'D', 'swift_code' => 'MSC', 'swift_label' => 'Miscellaneous'],
        'C5' => ['label' => 'Recrédit du Donneur d’Ordre', 'direction' => 'C', 'swift_code' => 'RTI', 'swift_label' => 'Returned item'],
    ];

    /** The French label of the CFONB: "Virements reçus" for "05". */
    public function label(): string
    {
        return $this->data()['label'];
    }

    /** Whether the operation debits or credits the account, or can do both. */
    public function direction(): Direction
    {
        return Direction::from($this->data()['direction']);
    }

    /** The SWIFT (MT940) transaction type of the code: "TRF" for "05". */
    public function swiftCode(): string
    {
        return $this->data()['swift_code'];
    }

    /** The label of the SWIFT transaction type: "Transfer" for "TRF". */
    public function swiftLabel(): string
    {
        return $this->data()['swift_label'];
    }

    /** @return array{label: string, direction: string, swift_code: string, swift_label: string} */
    private function data(): array
    {
        return self::DATA[$this->value];
    }
}

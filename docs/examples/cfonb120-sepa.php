<?php

use Silarhi\Cfonb\Banking\DetailQualifier;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\OperationCode;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('sepa.txt'));

// A SEPA direct debit, with the "05" detail records of its payer, beneficiary, references and mandate
$operation = $statements[0]->getOperations()[0];
$sepa = $operation->getSepaInformation();

var_dump([
    'code' => $operation->getOperationCode() === OperationCode::SepaDirectDebitsDomiciled,
    'fullLabel' => $operation->getFullLabel(),
    'payerName' => $sepa->getPayerName(),
    'beneficiaryName' => $sepa->getBeneficiaryName(),
    'creditorIdentifier' => $sepa->getBeneficiaryId(),
    'remittanceInformation' => $sepa->getRemittanceInformation(),
    'endToEndReference' => $sepa->getEndToEndReference(),
    'mandateReference' => $sepa->getMandateReference(),
    'sequenceType' => $sepa->getSequenceType(),
]);

// The raw detail records are still there, by qualifier
echo $operation->getDetail(DetailQualifier::PayerName)?->getAdditionalInformation(), "\n";

// A transfer received in US dollars
$originalAmount = $statements[0]->getOperations()[1]->getSepaInformation()->getOriginalAmount();
echo sprintf("%s %s at %s\n", $originalAmount?->getAmount(), $originalAmount?->getCurrencyCode(), $originalAmount?->getExchangeRate());

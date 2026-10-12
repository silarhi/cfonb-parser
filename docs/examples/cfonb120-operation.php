<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

// The first operation of the file, a SEPA direct debit with one detail line
$operation = $statements[0]->getOperations()[0];

var_dump([
    'bankCode' => $operation->getBankCode(),
    'deskCode' => $operation->getDeskCode(),
    'accountNumber' => $operation->getAccountNumber(),
    'internalCode' => $operation->getInternalCode(),
    'currencyCode' => $operation->getCurrencyCode(),
    'code' => $operation->getCode(),
    'date' => $operation->getDate()->format('Y-m-d'),
    'valueDate' => $operation->getValueDate()->format('Y-m-d'),
    'rejectCode' => $operation->getRejectCode(),
    'label' => $operation->getLabel(),
    'reference' => $operation->getReference(),
    'exemptCode' => $operation->getExemptCode(),
    'amount' => $operation->getAmount(),
    'bankOperationReference' => $operation->getBankOperationReference(),
]);

foreach ($operation->getDetails() as $detail) {
    echo sprintf("Detail %s: %s\n", $detail->getQualifier(), $detail->getAdditionalInformations());
}

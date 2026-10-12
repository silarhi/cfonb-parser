<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

$data = [];
foreach ($statements as $statement) {
    $statementData = [
        'account_number' => $statement->getOldBalance()->getAccountNumber(),
        'date' => $statement->getNewBalance()->getDate()->format('Y-m-d'),
        'opening_balance' => $statement->getOldBalance()->getAmount(),
        'closing_balance' => $statement->getNewBalance()->getAmount(),
        'operations' => [],
    ];

    foreach ($statement->getOperations() as $operation) {
        $statementData['operations'][] = [
            'date' => $operation->getDate()->format('Y-m-d'),
            'value_date' => $operation->getValueDate()->format('Y-m-d'),
            'amount' => $operation->getAmount(),
            'label' => $operation->getLabel(),
            'reference' => $operation->getReference(),
            'bank_code' => $operation->getBankCode(),
            'account_number' => $operation->getAccountNumber(),
        ];
    }

    $data[] = $statementData;
}

echo json_encode($data, JSON_PRETTY_PRINT), "\n";

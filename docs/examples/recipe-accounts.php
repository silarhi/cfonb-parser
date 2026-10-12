<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

// Group the statements by account
$accountStatements = [];
foreach ($statements as $statement) {
    $accountNumber = $statement->getOldBalance()->getAccountNumber();
    $accountStatements[$accountNumber][] = $statement;
}

foreach ($accountStatements as $accountNumber => $statementsOfAccount) {
    // The statements follow the order of the file: the last one holds the latest balance
    $last = $statementsOfAccount[array_key_last($statementsOfAccount)];

    echo sprintf("Account %s\n", $accountNumber);
    echo sprintf("  Statements: %d\n", count($statementsOfAccount));
    echo sprintf("  Final balance: %.2f\n", $last->getNewBalance()->getAmount());
}

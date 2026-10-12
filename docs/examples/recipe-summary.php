<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

$summary = ['total_credit' => 0.0, 'total_debit' => 0.0, 'operation_count' => 0];

foreach ($statements as $statement) {
    foreach ($statement->getOperations() as $operation) {
        $amount = $operation->getAmount();
        if ($amount > 0) {
            $summary['total_credit'] += $amount;
        } else {
            $summary['total_debit'] += abs($amount);
        }
        ++$summary['operation_count'];
    }
}

echo sprintf("Total credits: %.2f\n", $summary['total_credit']);
echo sprintf("Total debits: %.2f\n", $summary['total_debit']);
echo sprintf("Operations: %d\n", $summary['operation_count']);

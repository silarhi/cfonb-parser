<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach ($reader->parse(file_get_contents('statements.txt')) as $statement) {
    $opening = $statement->getOldBalance();
    $closing = $statement->getNewBalance();

    echo sprintf("Account %s, %s\n", $opening->getAccountNumber(), $opening->getCurrencyCode());
    echo sprintf("  %s  %-22s  %10.2f\n", $opening->getDate()->format('Y-m-d'), 'Opening balance', $opening->getAmount());

    foreach ($statement->getOperations() as $operation) {
        echo sprintf("  %s  %-22.22s  %10.2f\n", $operation->getDate()->format('Y-m-d'), $operation->getLabel(), $operation->getAmount());
    }

    echo sprintf("  %s  %-22s  %10.2f\n", $closing->getDate()->format('Y-m-d'), 'Closing balance', $closing->getAmount());
}

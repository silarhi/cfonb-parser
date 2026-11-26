<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

// Line by line from the file: one statement in memory at a time, whatever the size of the file
foreach ($reader->iterateFile('statements.txt') as $statement) {
    echo sprintf(
        "%s  %s → %s  %d operations\n",
        $statement->getAccount()->getAccountNumber(),
        $statement->getOldBalance()->getAmount(true),
        $statement->getNewBalance()->getAmount(true),
        count($statement->getOperations())
    );
}

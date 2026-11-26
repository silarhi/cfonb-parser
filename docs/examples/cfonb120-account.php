<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach ($reader->parse(file_get_contents('statements.txt')) as $statement) {
    $account = $statement->getAccount();

    echo sprintf("%s  key %s  %s\n", $account->getAccountNumber(), $account->getRibKey(), $account->getFormattedIban());
}

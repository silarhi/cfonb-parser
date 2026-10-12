<?php

use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

foreach ($statements as $statement) {
    $largeOperations = array_filter(
        $statement->getOperations(),
        fn (Operation $operation) => abs($operation->getAmount()) > 100
    );

    foreach ($largeOperations as $operation) {
        echo sprintf("Large operation: %.2f - %s\n", $operation->getAmount(), $operation->getLabel());
    }
}

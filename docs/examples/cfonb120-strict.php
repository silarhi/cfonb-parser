<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();
$content = file_get_contents('blank-references.txt');

// Strict mode (the default): a blank operation reference is an error
try {
    $reader->parse($content);
} catch (ParseException $e) {
    echo sprintf("Strict: %s\n", $e->getMessage());
}

// Non-strict mode: a blank operation reference becomes an empty string
foreach ($reader->parse($content, false) as $statement) {
    foreach ($statement->getOperations() as $operation) {
        echo sprintf("Non-strict: %s, reference %s\n", $operation->getLabel(), var_export($operation->getReference(), true));
    }
}

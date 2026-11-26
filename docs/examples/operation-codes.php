<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach ($reader->parse(file_get_contents('statements.txt')) as $statement) {
    foreach ($statement->getOperations() as $operation) {
        // null for a code the CFONB does not define
        $code = $operation->getOperationCode();

        echo sprintf(
            "%-22s  %s  %s (%s, %s)\n",
            $operation->getLabel(),
            $operation->getCode(),
            $code?->label() ?? 'Unknown code',
            $code?->swiftCode() ?? '-',
            $code?->direction()->name ?? '-'
        );
    }
}

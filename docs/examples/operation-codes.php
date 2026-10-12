<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\CfonbCodes;

$reader = new Cfonb120Reader();

foreach ($reader->parse(file_get_contents('statements.txt')) as $statement) {
    foreach ($statement->getOperations() as $operation) {
        $code = CfonbCodes::$codes[$operation->getCode()] ?? null;

        echo sprintf(
            "%-22s  %s  %s (%s, %s)\n",
            $operation->getLabel(),
            $operation->getCode(),
            $code['label'] ?? 'Unknown code',
            $code['swift_code'] ?? '-',
            $code['way'] ?? '-'
        );
    }
}

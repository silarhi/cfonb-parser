<?php

use Silarhi\Cfonb\CfonbReader;

$reader = new CfonbReader();

$statements = $reader->parseCfonb120(file_get_contents('statements.txt'));
$transfers = $reader->parseCfonb240(file_get_contents('transfers.txt'));

echo sprintf("%d statements, %d transfers\n", count($statements), count($transfers));

// Since 6.7, parse() detects the format from the first record code
foreach (['statements.txt', 'transfers.txt'] as $file) {
    $records = $reader->parse(file_get_contents($file));
    echo sprintf("%s: %d %s\n", $file, count($records), get_class($records[0]));
}

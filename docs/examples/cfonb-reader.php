<?php

use Silarhi\Cfonb\CfonbReader;

$reader = new CfonbReader();

$statements = $reader->parseCfonb120(file_get_contents('statements.txt'));
$transfers = $reader->parseCfonb240(file_get_contents('transfers.txt'));

echo sprintf("%d statements, %d transfers\n", count($statements), count($transfers));

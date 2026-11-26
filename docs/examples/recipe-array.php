<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse(file_get_contents('statements.txt'));

// Every object is JsonSerializable, through its toArray(): amounts are exact decimal strings, dates are Y-m-d
echo json_encode($statements[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";

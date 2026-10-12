<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();
$lines = file('statements.txt', FILE_IGNORE_NEW_LINES);

// A truncated file: the "07" new balance of the last statement is missing
try {
    $reader->parse(implode("\n", array_slice($lines, 0, -1)));
} catch (ParseException $e) {
    echo $e->getMessage(), "\n";
}

// A "04" operation before the "01" old balance of its statement
try {
    $reader->parse(implode("\n", [$lines[1], $lines[0], $lines[3]]));
} catch (ParseException $e) {
    echo $e->getMessage(), "\n";
}

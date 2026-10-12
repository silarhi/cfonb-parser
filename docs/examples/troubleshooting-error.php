<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();

// A line cut to 119 characters: no parser supports it
$content = substr(file_get_contents('statements.txt'), 0, 119);

try {
    $reader->parse($content);
} catch (ParseException $e) {
    echo 'Parse error: ', $e->getMessage(), "\n";
}

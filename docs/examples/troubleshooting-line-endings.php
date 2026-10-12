<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$content = file_get_contents('statements.txt');

$variants = [
    'LF' => $content,
    'CRLF' => str_replace("\n", "\r\n", $content),
    'CR' => str_replace("\n", "\r", $content),
    'No line breaks' => str_replace("\n", '', $content),
];

foreach ($variants as $name => $variant) {
    echo sprintf("%s: %d statements\n", $name, count($reader->parse($variant)));
}

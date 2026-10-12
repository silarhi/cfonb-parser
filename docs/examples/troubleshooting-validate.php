<?php

use Silarhi\Cfonb\Cfonb120Reader;

function validateCfonbFormat(string $content): bool
{
    // Normalize line endings, but keep trailing spaces: they are part of the 120 characters
    $lines = explode("\n", trim(str_replace("\r\n", "\n", $content), "\n"));

    foreach ($lines as $line) {
        if ('' === $line) {
            continue;
        }

        if (Cfonb120Reader::LINE_LENGTH !== strlen($line)) {
            return false;
        }

        if (!in_array(substr($line, 0, 2), ['01', '04', '05', '07'], true)) {
            return false;
        }
    }

    return true;
}

var_dump(validateCfonbFormat(file_get_contents('statements.txt')));
var_dump(validateCfonbFormat(file_get_contents('transfers.txt')));

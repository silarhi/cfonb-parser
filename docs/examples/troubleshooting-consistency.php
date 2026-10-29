<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ValidationException;

$reader = new Cfonb120Reader();
$content = file_get_contents('statements.txt');

foreach ($reader->parse($content) as $statement) {
    // Throws on the first inconsistency: another account, or a new balance that is not
    // the old balance plus the operations
    $statement->validate();
}
echo "Every statement is consistent\n";

// The new balance of the first statement, 16672.86, changed to 16672.85: one cent off
$content = preg_replace('/0000000166728F/', '0000000166728E', $content, 1);

try {
    foreach ($reader->parse($content) as $statement) {
        $statement->validate();
    }
} catch (ValidationException $e) {
    echo $e->getCheck()->name, ': ', $e->getMessage(), "\n";
}

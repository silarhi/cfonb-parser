<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) SILARHI <dev@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Silarhi\Cfonb\CfonbCodes;

// Runs the examples of the documentation with the parser itself, so the output the pages show is what the library
// really returns.
//
//     php docs/scripts/render-examples.php            # every example
//     php docs/scripts/render-examples.php --check    # fails when a committed output is missing or out of date (CI)
//
// Runs docs/examples/<example>.php from docs/examples/ (where the sample files are), writes its output to
// docs/src/data/examples/<example>.txt. Also dumps the operation codes of CfonbCodes to docs/src/data/operation-codes.json.

$examples = \dirname(__DIR__) . '/examples';
$data = \dirname(__DIR__) . '/src/data';
$output = "{$data}/examples";
$autoload = \dirname(__DIR__, 2) . '/vendor/autoload.php';
$check = \in_array('--check', \array_slice($argv ?? [], 1), true);

if (!is_file($autoload)) {
    fwrite(\STDERR, "Run composer install first.\n");
    exit(1);
}

require $autoload;

/** @var array<string, string> $renders path in docs/src/data => content */
$renders = [];
$files = glob("{$examples}/*.php");
foreach (false !== $files ? $files : [] as $file) {
    // Each example runs alone, as written: the autoloader is prepended, the ini settings make the output stable
    $command = [
        \PHP_BINARY,
        '-d', "auto_prepend_file={$autoload}",
        '-d', 'display_errors=stderr',
        '-d', 'error_reporting=-1',
        '-d', 'date.timezone=UTC',
        '-d', 'precision=14',
        '-d', 'serialize_precision=-1',
        $file,
    ];
    $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $examples);
    if (false === $process) {
        fwrite(\STDERR, "Unable to run {$file}\n");
        exit(1);
    }
    $stdout = (string) stream_get_contents($pipes[1]);
    $stderr = (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $name = basename($file, '.php');
    if (0 !== proc_close($process) || '' !== $stderr) {
        fwrite(\STDERR, "The example {$name} failed:\n{$stderr}{$stdout}");
        exit(1);
    }

    $renders["examples/{$name}.txt"] = $stdout;
}
// A list, not an object: JavaScript would order the numeric codes ("10") before the others ("01")
$codes = [];
foreach (CfonbCodes::$codes as $code => $description) {
    $codes[] = ['code' => (string) $code] + $description;
}
$renders['operation-codes.json'] = json_encode($codes, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR) . "\n";
ksort($renders);

// Outputs without an example any more
$stale = [];
$files = glob("{$output}/*.txt");
foreach (false !== $files ? $files : [] as $file) {
    if (!isset($renders['examples/' . basename($file)])) {
        $stale[] = 'examples/' . basename($file);
    }
}

if ($check) {
    $errors = [];
    foreach ($renders as $name => $render) {
        $committed = @file_get_contents("{$data}/{$name}");
        if (false === $committed) {
            $errors[] = "missing   {$name}";
        } elseif ($committed !== $render) {
            $errors[] = "outdated  {$name}";
        }
    }
    foreach ($stale as $name) {
        $errors[] = "stale     {$name}";
    }

    if ([] !== $errors) {
        fwrite(\STDERR, implode("\n", $errors) . "\n\nThe documentation examples do not match the parser: run php docs/scripts/render-examples.php and commit docs/src/data/.\n");
        exit(1);
    }

    echo \count($renders) . " examples up to date\n";
    exit(0);
}

@mkdir($output, 0o755, true);
foreach ($renders as $name => $render) {
    file_put_contents("{$data}/{$name}", $render);
}
foreach ($stale as $name) {
    unlink("{$data}/{$name}");
}

echo implode("\n", array_keys($renders)) . "\n";

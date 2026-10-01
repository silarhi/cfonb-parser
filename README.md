<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/v/silarhi/cfonb-parser?style=for-the-badge&label=stable&color=0d6efd&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/v/silarhi/cfonb-parser?style=for-the-badge&label=stable&color=0d6efd"
            alt="Latest Stable Version">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/dt/silarhi/cfonb-parser?style=for-the-badge&color=198754&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/dt/silarhi/cfonb-parser?style=for-the-badge&color=198754" alt="Total Downloads">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/l/silarhi/cfonb-parser?style=for-the-badge&color=6f42c1&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/l/silarhi/cfonb-parser?style=for-the-badge&color=6f42c1" alt="License">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/php-v/silarhi/cfonb-parser?style=for-the-badge&color=777bb4&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/php-v/silarhi/cfonb-parser?style=for-the-badge&color=777bb4" alt="PHP Version">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/github/actions/workflow/status/silarhi/cfonb-parser/continuous-integration.yml?style=for-the-badge&label=CI&color=20c997&branch=6.x&labelColor=1a1a2e">
        <img src="https://img.shields.io/github/actions/workflow/status/silarhi/cfonb-parser/continuous-integration.yml?style=for-the-badge&label=CI&color=20c997&branch=6.x"
            alt="CI Status">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fsilarhi%2Fcfonb-parser%2Fbadges%2Fcoverage.json&style=for-the-badge&labelColor=1a1a2e">
        <img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fsilarhi%2Fcfonb-parser%2Fbadges%2Fcoverage.json&style=for-the-badge" alt="Coverage">
    </picture>
</p>

<h1 align="center">CFONB Parser</h1>

<p align="center">
    <strong>Parse French CFONB 120 and 240 bank files into typed PHP objects.</strong><br>
    Bank statements and transfers, with zero runtime dependencies.
</p>

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Parse CFONB 120](#parse-cfonb-120)
    - [Parse CFONB 240](#parse-cfonb-240)
    - [Parse both CFONB 120 and CFONB 240](#parse-both-cfonb-120-and-cfonb-240)
- [Advanced Usage Examples](#advanced-usage-examples)
- [Troubleshooting](#troubleshooting)
- [Testing & Quality](#testing--quality)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Zero dependencies** — only PHP is required, nothing else gets pulled into your project.
- **CFONB 120 statements** — daily `Statement` objects with opening/closing balances (`01`/`07`), operations (`04`) and their details (`05`).
- **CFONB 240 transfers** — `Transfer` objects with a header (`31`), transactions (`34`) and a total (`39`).
- **Signed amounts** — decimals and sign characters are decoded: credits are positive, debits are negative.
- **Typed value objects** — getters return floats, `DateTimeInterface` dates and nullable strings, not raw arrays.
- **Tolerant input** — handles `\n` and `\r\n` line endings, and files with no line breaks at all.
- **Strict by default** — malformed lines throw a `ParseException` instead of producing silent garbage.

## Requirements

| Dependency | Version |
| ---------- | ------- |
| PHP        | 8.2+    |

## Installation

```bash
composer require silarhi/cfonb-parser
```

## Usage

### Parse CFONB 120

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

// Gets all statements day by day
foreach ($reader->parse('My Content') as $statement) {
    if ($statement->hasOldBalance()) {
        echo sprintf("Old balance : %f\n", $statement->getOldBalance()->getAmount());
    }

    foreach ($statement->getOperations() as $operation) {
        // Gets all statement operations
    }

    if ($statement->hasNewBalance()) {
        echo sprintf("New balance : %f\n", $statement->getNewBalance()->getAmount());
    }
}
```

#### Reading CFONB 120 from file

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$content = file_get_contents('/path/to/cfonb120-file.txt');

foreach ($reader->parse($content) as $statement) {
    // Process each daily statement
    echo "Statement date: " . $statement->getOldBalance()->getDate()->format('Y-m-d') . "\n";
}
```

#### Detailed operation processing

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach ($reader->parse($content) as $statement) {
    foreach ($statement->getOperations() as $operation) {
        echo "Date: " . $operation->getDate()->format('Y-m-d') . "\n";
        echo "Value Date: " . $operation->getValueDate()->format('Y-m-d') . "\n";
        echo "Amount: " . $operation->getAmount() . "\n";
        echo "Label: " . $operation->getLabel() . "\n";
        echo "Reference: " . $operation->getReference() . "\n";
        echo "Bank Code: " . $operation->getBankCode() . "\n";
        echo "Account: " . $operation->getAccountNumber() . "\n";

        // Access optional fields
        if ($operation->getInternalCode()) {
            echo "Internal Code: " . $operation->getInternalCode() . "\n";
        }

        // Process operation details (additional information)
        foreach ($operation->getDetails() as $detail) {
            echo "Additional Info: " . $detail->getAdditionalInformations() . "\n";
        }

        echo "---\n";
    }
}
```

#### Strict mode (default) vs non-strict mode

In strict mode, any line that cannot be parsed throws a `ParseException`. Non-strict mode only relaxes one rule: a blank
operation reference on a `04` line becomes an empty string instead of an error.

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();

// Strict mode (default) - throws an exception on parsing errors
try {
    $statements = $reader->parse($content); // strict=true by default
} catch (ParseException $e) {
    echo "Parsing error: " . $e->getMessage() . "\n";
}

// Non-strict mode - blank operation references are returned as ''
$statements = $reader->parse($content, false); // strict=false
```

### Parse CFONB 240

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\Banking\Transfer;

$reader = new Cfonb240Reader();

foreach ($reader->parse('My Content') as $transfer) {
    assert($transfer instanceof Transfer);
}
```

#### Detailed CFONB 240 transfer processing

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();

foreach ($reader->parse($content) as $transfer) {
    // Access transfer header information
    $header = $transfer->getHeader();
    echo "Transfer Date: " . $header->getPrevTransactionFileDate()->format('Y-m-d') . "\n";
    echo "Bank Code: " . $header->getRecipientBankCode1() . "\n";
    echo "Account: " . $header->getRecipientAccountNumber1() . "\n";

    // Process all transactions in the transfer
    foreach ($transfer->getTransactions() as $transaction) {
        echo "Transaction Date: " . $transaction->getSettlementDate()->format('Y-m-d') . "\n";
        echo "Amount: " . $transaction->getTransactionAmount() . "\n";
        echo "Recipient: " . $transaction->getRecipientName1() . "\n";
        echo "Reference: " . $transaction->getPresenterReference() . "\n";
    }

    // Access transfer total
    $total = $transfer->getTotal();
    echo "Total Amount: " . $total->getTotalAmount() . "\n";
    echo "Sequence Number: " . $total->getSequenceNumber() . "\n";
}
```

#### Reading CFONB 240 from file with error handling

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;

$reader = new Cfonb240Reader();

try {
    $content = file_get_contents('/path/to/cfonb240-file.txt');

    foreach ($reader->parse($content) as $transfer) {
        // Safely access optional header
        try {
            $header = $transfer->getHeader();
            echo "Processing transfer from: " . $header->getRecipientBankCode1() . "\n";
        } catch (HeaderUnavailableException $e) {
            echo "Transfer header not available\n";
        }

        // Process transactions...
    }
} catch (ParseException $e) {
    echo "Failed to parse CFONB file: " . $e->getMessage() . "\n";
}
```

### Parse both CFONB 120 and CFONB 240

```php
<?php

use Silarhi\Cfonb\CfonbReader;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Banking\Transfer;

$reader = new CfonbReader();

foreach ($reader->parseCfonb120('My Content') as $statement) {
    assert($statement instanceof Statement);
}

foreach ($reader->parseCfonb240('My Content') as $transfer) {
    assert($transfer instanceof Transfer);
}
```

## Advanced Usage Examples

### Processing bank statements with filtering

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Banking\Operation;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);

// Filter operations by amount
foreach ($statements as $statement) {
    $largeOperations = array_filter(
        $statement->getOperations(),
        fn(Operation $op) => abs($op->getAmount()) > 1000
    );

    foreach ($largeOperations as $operation) {
        echo "Large operation: {$operation->getAmount()} - {$operation->getLabel()}\n";
    }
}
```

### Building a transaction summary

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);

$summary = [
    'total_debit' => 0,
    'total_credit' => 0,
    'operation_count' => 0
];

foreach ($statements as $statement) {
    foreach ($statement->getOperations() as $operation) {
        $amount = $operation->getAmount();
        if ($amount > 0) {
            $summary['total_credit'] += $amount;
        } else {
            $summary['total_debit'] += abs($amount);
        }
        $summary['operation_count']++;
    }
}

echo "Summary:\n";
echo "Total Credits: {$summary['total_credit']}\n";
echo "Total Debits: {$summary['total_debit']}\n";
echo "Operation Count: {$summary['operation_count']}\n";
```

### Converting to array for API usage

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);

$data = [];
foreach ($statements as $statement) {
    $statementData = [
        'date' => $statement->getOldBalance()->getDate()->format('Y-m-d'),
        'opening_balance' => $statement->hasOldBalance() ? $statement->getOldBalance()->getAmount() : null,
        'closing_balance' => $statement->hasNewBalance() ? $statement->getNewBalance()->getAmount() : null,
        'operations' => []
    ];

    foreach ($statement->getOperations() as $operation) {
        $statementData['operations'][] = [
            'date' => $operation->getDate()->format('Y-m-d'),
            'value_date' => $operation->getValueDate()->format('Y-m-d'),
            'amount' => $operation->getAmount(),
            'label' => $operation->getLabel(),
            'reference' => $operation->getReference(),
            'bank_code' => $operation->getBankCode(),
            'account_number' => $operation->getAccountNumber()
        ];
    }

    $data[] = $statementData;
}

// Convert to JSON for API response
echo json_encode($data, JSON_PRETTY_PRINT);
```

### Handling multiple account statements

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);

// Group statements by account
$accountStatements = [];
foreach ($statements as $statement) {
    if ($statement->hasOldBalance()) {
        $accountNumber = $statement->getOldBalance()->getAccountNumber();
        $accountStatements[$accountNumber][] = $statement;
    }
}

// Process each account separately
foreach ($accountStatements as $accountNumber => $statements) {
    echo "Account: $accountNumber\n";
    echo "Number of statements: " . count($statements) . "\n";

    $totalBalance = 0;
    foreach ($statements as $statement) {
        if ($statement->hasNewBalance()) {
            $totalBalance = $statement->getNewBalance()->getAmount();
        }
    }
    echo "Final balance: $totalBalance\n\n";
}
```

## Troubleshooting

### Common parsing errors

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();

try {
    $statements = $reader->parse($content);
} catch (ParseException $e) {
    // Common issues:
    // - Invalid line length (should be 120 characters)
    // - Unknown record code (CFONB 120 supports 01, 04, 05 and 07)
    // - Malformed line format
    // - Invalid date format
    // - Invalid amount format

    echo "Parse error: " . $e->getMessage() . "\n";

    // Non-strict mode only tolerates blank operation references,
    // any other error is still thrown
    $statements = $reader->parse($content, false);
}
```

### Validating file format before parsing

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

function validateCfonbFormat(string $content): bool {
    // Normalize line endings, but keep trailing spaces: they are part of the 120 characters
    $lines = explode("\n", trim(str_replace("\r\n", "\n", $content), "\n"));

    foreach ($lines as $line) {
        // Skip empty lines
        if ('' === $line) continue;

        // Check line length for CFONB 120
        if (strlen($line) !== Cfonb120Reader::LINE_LENGTH) {
            return false;
        }

        // Check if line starts with valid CFONB code
        $lineCode = substr($line, 0, 2);
        if (!in_array($lineCode, ['01', '04', '05', '07'], true)) {
            return false;
        }
    }

    return true;
}

// Usage
if (validateCfonbFormat($content)) {
    $reader = new Cfonb120Reader();
    $statements = $reader->parse($content);
} else {
    echo "Invalid CFONB format\n";
}
```

## Testing & Quality

```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit

# Static analysis (PHPStan level: max)
vendor/bin/phpstan analyse

# Static analysis (Psalm)
vendor/bin/psalm

# Code style check
vendor/bin/php-cs-fixer fix --dry-run --diff

# Code style fix
vendor/bin/php-cs-fixer fix

# Code modernization check
vendor/bin/rector process --dry-run
```

## Contributing

Contributions are welcome! Please make sure your changes pass all quality checks before submitting a pull request:

```bash
vendor/bin/phpunit && vendor/bin/phpstan analyse && vendor/bin/psalm && vendor/bin/php-cs-fixer fix --dry-run --diff
```

## License

MIT License. See [LICENSE](LICENSE) for details.

---

<p align="center">
    Built with care by <a href="https://github.com/silarhi">SILARHI</a>.<br>
    If CFONB Parser saves you time, consider giving it a star on GitHub.
</p>

# cfonb-parser

![Build Status](https://github.com/silarhi/cfonb-parser/actions/workflows/continuous-integration.yml/badge.svg?branch=6.x)
[![Latest Stable Version](https://poser.pugx.org/silarhi/cfonb-parser/v/stable)](https://packagist.org/packages/silarhi/cfonb-parser)
[![Total Downloads](https://poser.pugx.org/silarhi/cfonb-parser/downloads)](https://packagist.org/packages/silarhi/cfonb-parser)
[![License](https://poser.pugx.org/silarhi/cfonb-parser/license)](https://packagist.org/packages/silarhi/cfonb-parser)

A zero dependencies PHP Parser for CFONB statements

Supports CFONB 120/240 format

## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require silarhi/cfonb-parser
```

## How to use

### Parse CFONB 120

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

//Gets all statements day by day
foreach($reader->parse('My Content') as $statement) {
  if ($statement->hasOldBalance()) {
    echo sprintf("Old balance : %f\n", $statement->getOldBalance()->getAmount());
  }
  foreach($statement->getOperations() as $operation) {
    //Gets all statement operations
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

foreach($reader->parse($content) as $statement) {
    // Process each daily statement
    echo "Statement date: " . $statement->getOldBalance()->getDate()->format('Y-m-d') . "\n";
}
```

#### Detailed operation processing

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach($reader->parse($content) as $statement) {
    foreach($statement->getOperations() as $operation) {
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
        foreach($operation->getDetails() as $detail) {
            echo "Additional Info: " . $detail->getAdditionalInformations() . "\n";
        }
        
        echo "---\n";
    }
}
```

#### Using strict mode (default) vs non-strict mode

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

$reader = new Cfonb120Reader();

// Strict mode (default) - throws exception on parsing errors
try {
    $statements = $reader->parse($content); // strict=true by default
} catch (ParseException $e) {
    echo "Parsing error: " . $e->getMessage() . "\n";
}

// Non-strict mode - continues parsing despite errors
$statements = $reader->parse($content, false); // strict=false
```

### Parse CFONB 240

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();

foreach($reader->parse('My Content') as $transfer) {
    assert($transfer instanceof \Silarhi\Cfonb\Banking\Transfer);
}
```

#### Detailed CFONB 240 transfer processing

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();

foreach($reader->parse($content) as $transfer) {
    // Access transfer header information
    $header = $transfer->getHeader();
    echo "Transfer Date: " . $header->getPrevTransactionFileDate()->format('Y-m-d') . "\n";
    echo "Bank Code: " . $header->getRecipientBankCode1() . "\n";
    echo "Account: " . $header->getRecipientAccountNumber1() . "\n";
    
    // Process all transactions in the transfer
    foreach($transfer->getTransactions() as $transaction) {
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

$reader = new Cfonb240Reader();

try {
    $content = file_get_contents('/path/to/cfonb240-file.txt');
    
    foreach($reader->parse($content) as $transfer) {
        // Safely access optional header
        try {
            $header = $transfer->getHeader();
            echo "Processing transfer from: " . $header->getRecipientBankCode1() . "\n";
        } catch (\Silarhi\Cfonb\Exceptions\HeaderUnavailableException $e) {
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

$reader = new CfonbReader();

foreach($reader->parseCfonb120('My Content') as $statement) {
    assert($statement instanceof \Silarhi\Cfonb\Banking\Statement);
}

foreach($reader->parseCfonb240('My Content') as $transfer) {
    assert($transfer instanceof \Silarhi\Cfonb\Banking\Transfer);
}
```

## Advanced Usage Examples

### Processing bank statements with filtering

```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);

// Filter operations by amount
foreach ($statements as $statement) {
    $largeOperations = array_filter(
        $statement->getOperations(), 
        fn(\Silarhi\Cfonb\Banking\Operation $op) => abs($op->getAmount()) > 1000
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
    // - Malformed line format
    // - Invalid date format
    // - Invalid amount format
    
    echo "Parse error on line: " . $e->getMessage() . "\n";
    
    // Try non-strict mode to continue parsing
    $statements = $reader->parse($content, false);
}
```

### Validating file format before parsing

```php
<?php

function validateCfonbFormat(string $content): bool {
    $lines = explode("\n", trim($content));
    
    foreach ($lines as $line) {
        // Skip empty lines
        if (empty(trim($line))) continue;
        
        // Check line length for CFONB 120
        if (strlen($line) !== 120) {
            return false;
        }
        
        // Check if line starts with valid CFONB code
        $lineCode = substr($line, 0, 2);
        if (!in_array($lineCode, ['01', '04', '05', '07'])) {
            return false;
        }
    }
    
    return true;
}

// Usage
if (validateCfonbFormat($content)) {
    $reader = new \Silarhi\Cfonb\Cfonb120Reader();
    $statements = $reader->parse($content);
} else {
    echo "Invalid CFONB format\n";
}
```

[composer]: http://getcomposer.org/

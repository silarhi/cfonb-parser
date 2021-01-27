# cfonb-parser
A PHP Parser for CFONB statements

Supports CFONB 120 signature and CFONB 240 transactions formats.
Helper for [EBICS Client PHP](https://github.com/andrew-svirin/ebics-client-php)

## How to use
```php
<?php

$parser = new \Silarhi\Cfonb\CfonbParser();

//Gets all statements day by day
foreach($parser->read120C('My Content') as $statement) {
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

```php
<?php

$parser = new \Silarhi\Cfonb\CfonbParser();

//Gets all statements day by day
foreach($parser->read240C('My Content') as $transaction) {
  if ($transaction->getHeader()) {
    echo sprintf("Old balance : %f\n", $transaction->getHeader()->getOperationCode()());
  }
  foreach($transaction->getOperations() as $operation) {
    //Gets all statement operations
  }
  
  if ($transaction->getTotal()()) {
    echo sprintf("New balance : %f\n", $transaction->getTotal()->getTotalAmount()());
  }
}
```

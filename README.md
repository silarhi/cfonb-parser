# cfonb-parser
![Tests](https://github.com/silarhi/cfonb-parser/workflows/Tests/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/silarhi/cfonb-parser/v/stable)](https://packagist.org/packages/silarhi/cfonb-parser)
[![Total Downloads](https://poser.pugx.org/silarhi/cfonb-parser/downloads)](https://packagist.org/packages/silarhi/cfonb-parser)
[![License](https://poser.pugx.org/silarhi/cfonb-parser/license)](https://packagist.org/packages/silarhi/cfonb-parser)

A PHP Parser for CFONB statements

Supports CFONB 120 format

## How to use
```php
<?php

use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$reader->parse('My Content');

//Gets all statements day by day
foreach($reader->getStatements() as $statement) {
  echo sprintf("Old balance : %f\n", $statement->getOldBalance()->getAmount());
  foreach($statement->getOperations() as $operation) {
      //Gets all statement operations
  }
  
  echo sprintf("New balance : %f\n", $statement->getNewBalance()->getAmount());
}
```

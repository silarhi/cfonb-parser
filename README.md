# cfonb-parser

![Build Status](https://github.com/silarhi/cfonb-parser/actions/workflows/continuous-integration.yml/badge.svg?branch=5.x)
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

### Parse CFONB 240

```php
<?php

use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();

foreach($reader->parse('My Content') as $transfer) {
    assert($transfer instanceof \Silarhi\Cfonb\Banking\Transfer);
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

[composer]: http://getcomposer.org/

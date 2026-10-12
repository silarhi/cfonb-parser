---
title: Exceptions
description: The exceptions thrown by the readers and the parsed objects.
sidebar:
    order: 5
---

All the exceptions are in the `Silarhi\Cfonb\Exceptions` namespace and extend `RuntimeException`.

| Exception                         | Extends            | Thrown                                                                                                      |
| --------------------------------- | ------------------ | ----------------------------------------------------------------------------------------------------------- |
| `ParseException`                  | `RuntimeException` | By `parse()`, on a line the reader cannot parse: [messages](/guides/troubleshooting/#common-parsing-errors) |
| `ValueOfKeyIsNotAStringException` | `ParseException`   | By `parse()`, on a blank required field                                                                     |
| `ValueOfKeyIsNotNumericException` | `ParseException`   | By `parse()`, on a blank numeric field                                                                      |
| `KeyDoesNotExistException`        | `ParseException`   | Never in practice: an internal error of a line parser                                                       |
| `BalanceUnavailableException`     | `RuntimeException` | By `Statement::getOldBalance()` and `getNewBalance()`, on a statement built by hand without them            |
| `HeaderUnavailableException`      | `RuntimeException` | By `Transfer::getHeader()`, on a transfer built by hand without one                                         |
| `TotalUnavailableException`       | `RuntimeException` | By `Transfer::getTotal()`, on a transfer built by hand without one                                          |

Catch `ParseException` around `parse()`: it covers its three subclasses.

```php
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

try {
    $statements = (new Cfonb120Reader())->parse($content);
} catch (ParseException $e) {
    // The file is not a valid CFONB 120 file
}
```

The statements and transfers returned by the readers always have their balances, header and total: the
`*UnavailableException` exceptions are never thrown on them.

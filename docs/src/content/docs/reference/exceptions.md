---
title: Exceptions
description: The exceptions thrown by the readers and the parsed objects.
sidebar:
    order: 5
---

All the exceptions are in the `Silarhi\Cfonb\Exceptions` namespace and extend `RuntimeException`.

| Exception                         | Extends            | Thrown                                                                                                                               |
| --------------------------------- | ------------------ | ------------------------------------------------------------------------------------------------------------------------------------ |
| `ParseException`                  | `RuntimeException` | By `parse()`, on a line the reader cannot parse, or records out of order: [messages](/guides/troubleshooting/#common-parsing-errors) |
| `ValidationException`             | `ParseException`   | By `Statement::validate()` and `Transfer::validate()`, on a statement or a transfer that is not consistent (since 6.6)               |
| `ValueOfKeyIsNotAStringException` | `ParseException`   | By `parse()`, on a blank required field                                                                                              |
| `ValueOfKeyIsNotNumericException` | `ParseException`   | By `parse()`, on a blank numeric field                                                                                               |
| `KeyDoesNotExistException`        | `ParseException`   | Never in practice: an internal error of a line parser                                                                                |
| `BalanceUnavailableException`     | `RuntimeException` | By `Statement::getOldBalance()` and `getNewBalance()`, on a statement built by hand without them                                     |
| `HeaderUnavailableException`      | `RuntimeException` | By `Transfer::getHeader()`, on a transfer built by hand without one                                                                  |
| `TotalUnavailableException`       | `RuntimeException` | By `Transfer::getTotal()`, on a transfer built by hand without one                                                                   |

Catch `ParseException` around `parse()`: it covers its subclasses.

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

## The line that failed

Since 6.6, the messages do not hold the line that failed to parse: it holds account numbers and labels. A
`ParseException` about a line keeps it, for debugging, in `getRawContent(): ?string`; `null` when the error is not about
a single line, e.g. records out of order. Do not log it or show it as is.

## ValidationCheck

`ValidationException::getCheck()` returns the check that failed, a `Silarhi\Cfonb\Exceptions\ValidationCheck` enum
([checking the consistency of a file](/guides/troubleshooting/#checking-the-consistency-of-a-file)):

| Case            | Value            | Failed check                                                                                        |
| --------------- | ---------------- | --------------------------------------------------------------------------------------------------- |
| `BankCode`      | `bank_code`      | A record has another bank code than the `01` old balance (CFONB 120) or the `31` header (CFONB 240) |
| `DeskCode`      | `desk_code`      | A record has another desk (counter) code                                                            |
| `AccountNumber` | `account_number` | A record has another account number                                                                 |
| `Currency`      | `currency`       | A record has another currency (CFONB 240: currency index)                                           |
| `Balance`       | `balance`        | CFONB 120: the `07` new balance is not the `01` old balance plus the `04` operations                |
| `Total`         | `total`          | CFONB 240: the `39` total is not the sum of the `34` transactions                                   |

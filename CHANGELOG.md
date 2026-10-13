# 6.5

## Upgrade note

**6.5 rejects files that 6.4 accepted.** The record order of CFONB 120 and 240 files and the dates are now checked, in
strict and non-strict mode alike: a file whose records are out of order, or that holds a zero-filled or out-of-range
mandatory date, now throws a `ParseException` where 6.4 returned wrong data silently. `$strict = false` does not restore
the 6.4 behaviour. Before upgrading, parse a sample of the files you receive with 6.5 and catch `ParseException`: the
[troubleshooting guide](https://cfonb-parser.silarhi.dev/guides/troubleshooting/) lists the new messages.

## Changed

Files that parsed with 6.4 can now throw a `ParseException`: every case below used to return wrong data silently.

- A zero-filled date (`000000`) now throws `Unable to parse date "000000": the date is zero-filled (empty), but this field is mandatory`, in strict and non-strict mode alike. Some banks write a date they do not have, e.g. the value date of an operation, as zeros: 6.4 read it as 30 November 1999. The CFONB 120 dates, the value date included, are mandatory, so their getters stay non-nullable: fix the file, or replace the date before parsing it. The CFONB 240 initial transaction settlement date, the only optional date, is `null` instead (see Fixed)
- An out-of-range date (e.g. day 32 or month 13) now throws a `ParseException` instead of overflowing into the next month
- CFONB 120: the records of a statement must come in the order `01` old balance, `04` operations with their `05` details, `07` new balance, in strict and non-strict mode alike. 6.4 paired the balance lines by their order, not by their code, so a `07` before any `01` became the old balance of a statement; it now throws, as does a `04` operation before any `01` (6.4 added it to the next statement), a `01` while a statement is still open (6.4 made it the new balance) and a statement still open at the end of the file (6.4 dropped it)
- CFONB 240: a `34` or `39` record after a `39` total and before the next `31` header now throws a `ParseException` instead of being attached to the closed transfer
- CFONB 240: a `31` header while a transfer is still open, or a transfer still open at the end of the file, now throws a `ParseException` instead of the transfer being dropped

## Fixed

- CFONB 120: an amount with 0 decimals (position 20) is now parsed as an integer instead of a garbage value
- CFONB 240: a `34` or `39` record before any `31` header now reports the missing header
- CFONB 240: a zero-filled initial transaction settlement date (`000000`) is now `null`, like a blank one, instead of 30 November 1999

## Added

- Support `\r` (classic Mac OS) line endings

# 6.4

## Changed

- 6.4.2: CFONB 120 and 240 parsing is about 20% faster, with 12–17% less peak memory, and gives the same results

6.4.0 and 6.4.1 hold no change to the library itself: same code as 6.3, development tooling only.

# 6.3

## Changed

- [BC] Every class is now `final`: the `Banking` objects (`Statement`, `Balance`, `Operation`, `OperationDetail`,
  `Transfer`, `Header`, `Transaction`, `Total`), `CfonbCodes` and the readers, which are also `readonly`
  (`final readonly class Cfonb120Reader`, `Cfonb240Reader` and `CfonbReader`). Wrap them instead of extending them

# 6.2

## Fixed

- 6.2.1: CFONB 120: `Operation::getBankOperationReference()` accepts any character, not only letters, digits and
  spaces: a reference holding e.g. `-` or `/` no longer makes the `04` line fail to parse

# 6.1

## Added

- CFONB 120: `Operation::getBankOperationReference()`, the bank operation reference (positions 105 to 120 of the `04`
  line) that identifies an operation, `null` when blank

# 6.0

Drop support for php 8.1

# 5.0

Add a strict mode parameter

# 4.0

Bump minimal php version to 8.1

# 3.0

Bump minimal php version to 8.0

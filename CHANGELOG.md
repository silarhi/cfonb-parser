# 6.7.1

## Fixed

- `ext-ctype` is required: `SepaInformation` reads its amounts and rates with `ctype_digit()`, and nothing installed
  the extension or its polyfill

# 6.7

## Added

- SEPA information, decoded from the `05` detail records by their qualifier (CFONB, _Evolutions du relevé de compte 120
  caractères_, version 2.0): `Operation::getSepaInformation()` returns a `SepaInformation` with the payer, the
  beneficiary, the ultimate debtor and creditor (names, identifiers and their types), the remittance information (the
  `LCC` and `LC2` lines joined, up to 140 characters), the structured remittance information, the end-to-end reference
  and the purpose, the remittance and transaction references, the original amount and exchange rate of a transfer
  (`OriginalAmount`), the accounts, and the mandate reference and sequence type of a direct debit. Each getter is `null`
  when the bank sends no detail record of its qualifier
- `Operation::getFullLabel()`: the label followed by the `LIB` detail lines. `Operation::getDetailsByQualifier()` and
  `Operation::getDetail()` find the details of a qualifier, given as a string or a `DetailQualifier` enum, the 18
  qualifiers of the CFONB with their French labels
- `OperationDetail::getDetailQualifier()`, `OperationDetail::getAdditionalInformationField()` (a field of the
  additional information at a fixed offset, for the qualifiers that hold two), and `OperationDetail::getOperationCode()`
- The `OperationCode` enum, the CFONB operation codes of `CfonbCodes::$codes` with `label()`, `direction()` (a
  `Direction` enum: `Debit`, `Credit`, `DebitOrCredit`, `CreditOrDebit`), `swiftCode()` and `swiftLabel()`.
  `Operation::getOperationCode()` returns the case of an operation, `null` for a code the CFONB does not define
- `Account`, built by `Statement::getAccount()`, `Balance::getAccount()` and `Operation::getAccount()`: the bank code,
  desk code and account number, with the RIB key (`getRibKey()`), the BBAN and the IBAN (`getIban()`,
  `getFormattedIban()`; `FR` by default, `MC` for Monaco)
- `CfonbReader::parse()` reads a CFONB 120 or a CFONB 240 file, detected by the record code of its first line with the
  new `CfonbFormat` enum (`CfonbFormat::fromContent()`, `tryFromContent()`)
- `Cfonb120Reader::iterate()` and `Cfonb240Reader::iterate()` yield the statements and transfers one by one, each as soon
  as its closing record is read. `iterateFile()` reads a file line by line, so that the memory used does not grow with
  the size of the file, and throws a `RuntimeException` when the file cannot be opened
- `toArray()` and `JsonSerializable` on `Statement`, `Balance`, `Operation`, `OperationDetail`, `Transfer`, `Header`,
  `Transaction`, `Total`, `SepaInformation`, `OriginalAmount` and `Account`: snake_case keys, dates as `Y-m-d`, amounts
  as exact decimal strings
- Constructors that build a statement, a transfer or an operation at once: `new Statement($oldBalance, $newBalance,
$operations)`, `new Transfer($header, $transactions, $total)`, and the details as the last argument of `Operation`.
  `Operation::withDetails()` returns a copy with other details. The readers build them this way
- `Transaction::getCurrencyIndex()`, `Transaction::getAmount()`, `Total::getAmount()` and
  `OperationDetail::getAdditionalInformation()`, the names of the getters they replace (see Deprecated)

## Changed

- `parse()` returns a list (`list<Statement>`, `list<Transfer>`) for static analysis, and `getOperations()`,
  `getDetails()` and `getTransactions()` too: the values are the same

## Fixed

- The label of the operation code `A5` is "Prélèvements SEPA – reversements émis (par le créancier ou sa banque)": it
  was the label of `A6`, the business-to-business (_interentreprises_) one

## Deprecated

7.0 removes them:

- `CfonbCodes`: use the `OperationCode` enum, `OperationCode::tryFrom($code)?->label()`
- `Transaction::getCurIndex()`, `Transaction::getTransactionAmount()`, `Total::getTotalAmount()` and
  `OperationDetail::getAdditionalInformations()`: use `getCurrencyIndex()`, `getAmount()` and
  `getAdditionalInformation()`
- The setters of the parsed objects, which 7.0 makes immutable: `Statement::setOldBalance()`, `setNewBalance()` and
  `addOperation()`, `Transfer::setHeader()`, `addTransaction()` and `setTotal()`, `Operation::addDetails()`. Pass the
  values to the constructors, or use `Operation::withDetails()`

# 6.6

## Added

- `Statement::validate()` and `Transfer::validate()` check the consistency of a statement or a transfer, which the
  readers do not: they throw a `ValidationException` (a `ParseException`) whose `getCheck()` tells which
  `ValidationCheck` failed. Opt-in: call them on the statements and transfers you need to trust
    - CFONB 120: the `07` new balance, the `04` operations and their `05` details belong to the account of the `01` old
      balance (desk code, account number, currency; bank code between the two balances only), and the new balance is
      the old balance plus the operations
    - CFONB 240: the `39` total belongs to the account of the `31` header (bank code, counter code, account number,
      currency index), the `34` transactions have its currency index, and the total is the sum of the transactions
    - The amounts are added as integers in minor units, never as floats
- Exact amounts, as decimal strings: pass `true` to `Balance::getAmount()`, `Operation::getAmount()`,
  `Transaction::getTransactionAmount()` and `Total::getTotalAmount()`. `getAmount(true)` is `"-22.79"`, with the
  number of decimals of the line (`"1669565"` with 0 decimals), ready for a `DECIMAL` column or bcmath; the float loses
  cents, `(int) (0.29 * 100)` being `28`. The return type follows the argument for PHPStan and Psalm (`float` by
  default, `string` with `true`). The objects keep the amount as this string and compute the float from it, the same
  float as before. Their constructors take the amount as `float|string`: a float becomes the decimal string that gives
  it back, `1669.565` staying `"1669.565"`, and a string that is not a decimal throws an `InvalidArgumentException`
- `ParseException::getRawContent()`: the line (or, for an amount, the field) that failed to parse, now kept out of the
  message

## Changed

- The messages of a `ParseException` no longer hold the line that failed, which holds account numbers and labels and
  ended up in logs and error pages. Code matching the full message must be updated; the beginning of the messages is
  unchanged:
    - `Unable to find a parser for the line :` + the line is now `Unable to find a parser for the line 3: record code "04", 119 characters`
    - `Regex does not match the line "…"` is now `Regex does not match the line: the field "amount" at position 91 is invalid`
    - `Unable to parse amount "…"` is now `Unable to parse amount: its last character "…" is not a sign character`

## Deprecated

- The float returned by `getAmount()`, `getTransactionAmount()` and `getTotalAmount()` without an argument: 7.0 returns
  the exact decimal string only. Pass `true` to get it now

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

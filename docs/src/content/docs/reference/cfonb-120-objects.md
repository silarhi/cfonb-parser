---
title: CFONB 120 objects
description: Statement, Balance, Operation, OperationDetail, SepaInformation and Account, the objects of a parsed CFONB 120 file.
sidebar:
    order: 2
---

The objects of the `Silarhi\Cfonb\Banking` namespace returned by
[`Cfonb120Reader`](/reference/readers/#cfonb120reader). Positions are the characters of the line, from 1.

Strings are trimmed. A nullable getter returns `null` when its field is blank; a field read by a non-nullable getter
must not be blank, or parsing fails (see [strict mode](/guides/cfonb-120/#strict-mode-default-vs-non-strict-mode) for
the reference). Dates are `DateTimeImmutable` at midnight and mandatory, a zero-filled date fails too:
[dates](/guides/cfonb-120/#dates). Amounts are signed floats, and exact decimal strings with `true` since 6.6:
[amounts](/guides/cfonb-120/#amounts).

## Statement

The balances and operations between an opening and a closing balance line.

| Method                                           | Returns                                                        |
| ------------------------------------------------ | -------------------------------------------------------------- |
| `getOldBalance(): Balance`                       | The opening balance (`01`)                                     |
| `getNewBalance(): Balance`                       | The closing balance (`07`)                                     |
| `hasOldBalance(): bool`, `hasNewBalance(): bool` | Whether the balance is set: always `true` on parsed statements |
| `getOperations(): Operation[]`                   | The operations (`04`), in the order of the file                |
| `getAccount(): Account`                          | Since 6.7: the [account](#account) of the old balance          |
| `validate(): void`                               | Since 6.6: checks the consistency of the statement             |
| `toArray(): array`                               | Since 6.7: the statement as an array, for JSON                 |

`validate()` throws a `ValidationException` when an operation belongs to another account or the new balance is not the
old balance plus the operations: [checking the consistency of a file](/guides/troubleshooting/#checking-the-consistency-of-a-file).

`getOldBalance()` and `getNewBalance()` throw a `BalanceUnavailableException` when the balance is not set, which only
happens on a `Statement` built by hand. Since 6.7, a statement is built with its constructor,
`new Statement($oldBalance, $newBalance, $operations)`: `setOldBalance()`, `setNewBalance()` and `addOperation()` are
deprecated, and 7.0 makes the statement immutable.

Every object of this page implements `JsonSerializable` since 6.7: `toArray()`, which `json_encode()` uses, gives its
fields in snake_case, the dates as `Y-m-d` and the amounts as exact decimal strings.

## Balance

An opening (`01`) or closing (`07`) balance line.

| Method                                             | Field                                                   | Positions                                          |
| -------------------------------------------------- | ------------------------------------------------------- | -------------------------------------------------- |
| `getBankCode(): string`                            | Bank code                                               | 3–7                                                |
| `getDeskCode(): string`                            | Desk code                                               | 12–16                                              |
| `getCurrencyCode(): string`                        | Currency, ISO                                           | 17–19                                              |
| `getAccountNumber(): string`                       | Account number                                          | 22–32                                              |
| `getDate(): DateTimeImmutable`                     | Balance date                                            | 35–40                                              |
| `getAmount(bool $asString = false): float\|string` | Balance: float, or exact string with `true` (since 6.6) | 91–104, with the number of decimals of position 20 |
| `getAccount(): Account`                            | Since 6.7: the [account](#account), with its IBAN       |                                                    |

## Operation

An operation line (`04`).

| Method                                             | Field                                                  | Positions                                          |
| -------------------------------------------------- | ------------------------------------------------------ | -------------------------------------------------- |
| `getBankCode(): string`                            | Bank code                                              | 3–7                                                |
| `getInternalCode(): ?string`                       | Internal operation code of the bank                    | 8–11                                               |
| `getDeskCode(): string`                            | Desk code                                              | 12–16                                              |
| `getCurrencyCode(): ?string`                       | Currency, ISO                                          | 17–19                                              |
| `getAccountNumber(): string`                       | Account number                                         | 22–32                                              |
| `getCode(): string`                                | [Operation code](/reference/operation-codes/)          | 33–34                                              |
| `getDate(): DateTimeImmutable`                     | Operation date                                         | 35–40                                              |
| `getRejectCode(): ?string`                         | Reject code                                            | 41–42                                              |
| `getValueDate(): DateTimeImmutable`                | Value date                                             | 43–48                                              |
| `getLabel(): string`                               | Label                                                  | 49–79                                              |
| `getReference(): string`                           | Reference, `''` when blank in non-strict mode          | 82–88                                              |
| `getExemptCode(): ?string`                         | Exempt code                                            | 89                                                 |
| `getAmount(bool $asString = false): float\|string` | Amount: float, or exact string with `true` (since 6.6) | 91–104, with the number of decimals of position 20 |
| `getBankOperationReference(): ?string`             | Bank operation reference                               | 105–120                                            |
| `getDetails(): OperationDetail[]`                  | The detail lines (`05`) following it                   |                                                    |

Since 6.7:

| Method                                                             | Returns                                                                                                   |
| ------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------- |
| `getOperationCode(): ?OperationCode`                               | The [operation code](/reference/operation-codes/) as an enum, `null` for a code the CFONB does not define |
| `getFullLabel(): string`                                           | The label, then the `LIB` detail lines, separated by a space                                              |
| `getDetailsByQualifier(DetailQualifier\|string $qualifier): array` | The detail lines of this qualifier, in order                                                              |
| `getDetail(DetailQualifier\|string $qualifier): ?OperationDetail`  | The first detail line of this qualifier                                                                   |
| `getSepaInformation(): SepaInformation`                            | The [SEPA information](#sepainformation) decoded from the detail lines                                    |
| `getAccount(): Account`                                            | The [account](#account) of the operation                                                                  |
| `withDetails(OperationDetail ...$details): Operation`              | A copy of the operation with these detail lines                                                           |

The constructor takes the details as its last argument. `addDetails()` is deprecated: 7.0 makes the operation immutable.

## OperationDetail

A detail line (`05`), complementing the operation line before it.

| Method                                | Field                                    | Positions |
| ------------------------------------- | ---------------------------------------- | --------- |
| `getBankCode(): string`               | Bank code                                | 3–7       |
| `getInternalCode(): ?string`          | Internal operation code of the bank      | 8–11      |
| `getDeskCode(): string`               | Desk code                                | 12–16     |
| `getCurrencyCode(): ?string`          | Currency, ISO                            | 17–19     |
| `getAccountNumber(): string`          | Account number                           | 22–32     |
| `getCode(): string`                   | Operation code                           | 33–34     |
| `getDate(): DateTimeImmutable`        | Operation date                           | 35–40     |
| `getQualifier(): string`              | Qualifier of the information, e.g. `LIB` | 46–48     |
| `getAdditionalInformation(): ?string` | Additional information (since 6.7)       | 49–118    |

Since 6.7, `getDetailQualifier(): ?DetailQualifier` gives the qualifier as an enum, `getOperationCode(): ?OperationCode`
the operation code, and `getAdditionalInformationField(int $offset, int $length): ?string` a field of the additional
information, from offset 0 (position 49). `getAdditionalInformations()` is deprecated.

## SepaInformation

Since 6.7, `Operation::getSepaInformation()` decodes the detail lines of the SEPA qualifiers, defined by the CFONB in
_Evolutions du relevé de compte 120 caractères_ (version 2.0, March 2010). Every getter is `null` when the bank sends no
line of its qualifier, and reads the first line when it sends several.

| Method                                                                                | Qualifier and field                                               |
| ------------------------------------------------------------------------------------- | ----------------------------------------------------------------- |
| `getPayerName()`                                                                      | `NPY`, positions 49–118                                           |
| `getPayerId()`, `getPayerIdType()`                                                    | `IPY`, positions 49–83 and 84–118                                 |
| `getBeneficiaryName()`                                                                | `NBE`                                                             |
| `getBeneficiaryId()`, `getBeneficiaryIdType()`                                        | `IBE`: the SEPA creditor identifier (ICS) of a direct debit       |
| `getUltimateDebtorName()`, `getUltimateDebtorId()`, `getUltimateDebtorIdType()`       | `NPO`, `IPO`                                                      |
| `getUltimateCreditorName()`, `getUltimateCreditorId()`, `getUltimateCreditorIdType()` | `NBU`, `IBU`                                                      |
| `getRemittanceInformation()`                                                          | `LCC` then `LC2`, joined as they are: up to 140 characters        |
| `getStructuredRemittanceInformation()`                                                | `LCS`, the creditor reference                                     |
| `getEndToEndReference()`, `getPurpose()`                                              | `RCN`, positions 49–83 and 84–118                                 |
| `getRemittanceReference()`, `getTransactionReference()`                               | `REF`, positions 49–83 and 84–118                                 |
| `getOriginalAmount(): ?OriginalAmount`                                                | `MMO`: currency, unsigned exact amount, exchange rate             |
| `getBeneficiaryAccount()`                                                             | `CBE`, transfers                                                  |
| `getMandateReference()`, `getSequenceType()`                                          | `RUM`: the mandate reference, then `OOFF`, `FRST`, `RCUR`, `FNAL` |
| `getDebtorAccount()`                                                                  | `CPY`, direct debits                                              |

`getOriginalAmount()` is `null` too when the currency or the amount of the `MMO` line is not filled in as the CFONB
defines them. `OriginalAmount` has `getCurrencyCode()`, `getAmount()` (a decimal string) and `getExchangeRate()` (a
decimal string, `null` when the bank gives none).

The `DetailQualifier` enum names the 18 qualifiers: `DetailQualifier::PayerName` is `NPY`, and `label()` gives its French
label.

## Account

Since 6.7, `Statement::getAccount()`, `Balance::getAccount()` and `Operation::getAccount()` return the account as an
`Account`, which computes the RIB key and the IBAN from the bank code, the desk code and the account number.

| Method                                                 | Returns                                                |
| ------------------------------------------------------ | ------------------------------------------------------ |
| `getBankCode()`, `getDeskCode()`, `getAccountNumber()` | The fields of the account                              |
| `getRibKey(): string`                                  | The 2 digits of the RIB key                            |
| `getBban(): string`                                    | The 23 characters of the French BBAN, with the RIB key |
| `getIban(string $countryCode = 'FR'): string`          | The IBAN without spaces; `MC` for an account in Monaco |
| `getFormattedIban(string $countryCode = 'FR'): string` | The IBAN in groups of 4 characters                     |

`(string) $account` is the IBAN. The constructor throws an `InvalidArgumentException` when the bank code or the desk
code is not 5 digits, or the account number not 11 digits or capital letters.

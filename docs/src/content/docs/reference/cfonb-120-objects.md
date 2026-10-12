---
title: CFONB 120 objects
description: Statement, Balance, Operation and OperationDetail, the objects of a parsed CFONB 120 file.
sidebar:
    order: 2
---

The objects of the `Silarhi\Cfonb\Banking` namespace returned by
[`Cfonb120Reader`](/reference/readers/#cfonb120reader). Positions are the characters of the line, from 1.

Strings are trimmed. A nullable getter returns `null` when its field is blank; a field read by a non-nullable getter
must not be blank, or parsing fails (see [strict mode](/guides/cfonb-120/#strict-mode-default-vs-non-strict-mode) for
the reference). Dates are `DateTimeImmutable` at midnight, amounts are signed floats:
[amounts](/guides/cfonb-120/#amounts).

## Statement

The balances and operations between an opening and a closing balance line.

| Method                                           | Returns                                                        |
| ------------------------------------------------ | -------------------------------------------------------------- |
| `getOldBalance(): Balance`                       | The opening balance (`01`)                                     |
| `getNewBalance(): Balance`                       | The closing balance (`07`)                                     |
| `hasOldBalance(): bool`, `hasNewBalance(): bool` | Whether the balance is set: always `true` on parsed statements |
| `getOperations(): Operation[]`                   | The operations (`04`), in the order of the file                |

`getOldBalance()` and `getNewBalance()` throw a `BalanceUnavailableException` when the balance is not set, which only
happens on a `Statement` built by hand. `setOldBalance()`, `setNewBalance()` and `addOperation()` build the statement;
the reader calls them.

## Balance

An opening (`01`) or closing (`07`) balance line.

| Method                         | Field          | Positions                                          |
| ------------------------------ | -------------- | -------------------------------------------------- |
| `getBankCode(): string`        | Bank code      | 3–7                                                |
| `getDeskCode(): string`        | Desk code      | 12–16                                              |
| `getCurrencyCode(): string`    | Currency, ISO  | 17–19                                              |
| `getAccountNumber(): string`   | Account number | 22–32                                              |
| `getDate(): DateTimeImmutable` | Balance date   | 35–40                                              |
| `getAmount(): float`           | Balance        | 91–104, with the number of decimals of position 20 |

## Operation

An operation line (`04`).

| Method                                 | Field                                         | Positions                                          |
| -------------------------------------- | --------------------------------------------- | -------------------------------------------------- |
| `getBankCode(): string`                | Bank code                                     | 3–7                                                |
| `getInternalCode(): ?string`           | Internal operation code of the bank           | 8–11                                               |
| `getDeskCode(): string`                | Desk code                                     | 12–16                                              |
| `getCurrencyCode(): ?string`           | Currency, ISO                                 | 17–19                                              |
| `getAccountNumber(): string`           | Account number                                | 22–32                                              |
| `getCode(): string`                    | [Operation code](/reference/operation-codes/) | 33–34                                              |
| `getDate(): DateTimeImmutable`         | Operation date                                | 35–40                                              |
| `getRejectCode(): ?string`             | Reject code                                   | 41–42                                              |
| `getValueDate(): DateTimeImmutable`    | Value date                                    | 43–48                                              |
| `getLabel(): string`                   | Label                                         | 49–79                                              |
| `getReference(): string`               | Reference, `''` when blank in non-strict mode | 82–88                                              |
| `getExemptCode(): ?string`             | Exempt code                                   | 89                                                 |
| `getAmount(): float`                   | Amount                                        | 91–104, with the number of decimals of position 20 |
| `getBankOperationReference(): ?string` | Bank operation reference                      | 105–120                                            |
| `getDetails(): OperationDetail[]`      | The detail lines (`05`) following it          |                                                    |

`addDetails(OperationDetail $details)` adds a detail; the reader calls it.

## OperationDetail

A detail line (`05`), complementing the operation line before it.

| Method                                 | Field                                    | Positions |
| -------------------------------------- | ---------------------------------------- | --------- |
| `getBankCode(): string`                | Bank code                                | 3–7       |
| `getInternalCode(): ?string`           | Internal operation code of the bank      | 8–11      |
| `getDeskCode(): string`                | Desk code                                | 12–16     |
| `getCurrencyCode(): ?string`           | Currency, ISO                            | 17–19     |
| `getAccountNumber(): string`           | Account number                           | 22–32     |
| `getCode(): string`                    | Operation code                           | 33–34     |
| `getDate(): DateTimeImmutable`         | Operation date                           | 35–40     |
| `getQualifier(): string`               | Qualifier of the information, e.g. `LIB` | 46–48     |
| `getAdditionalInformations(): ?string` | Additional information                   | 49–118    |

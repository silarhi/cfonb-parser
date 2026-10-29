---
title: CFONB 240 objects
description: Transfer, Header, Transaction and Total, the objects of a parsed CFONB 240 file.
sidebar:
    order: 3
---

The objects of the `Silarhi\Cfonb\Banking` namespace returned by
[`Cfonb240Reader`](/reference/readers/#cfonb240reader). Positions are the characters of the line, from 1.

Strings are trimmed. A nullable getter returns `null` when its field is blank; a field read by a non-nullable getter
must not be blank, or parsing fails. Dates are `DateTimeInterface` (`DateTimeImmutable` at midnight), amounts are
floats in the currency, never negative: [amounts](/guides/cfonb-240/#amounts).

The `…2` fields repeat the `…1` ones, positions 22 to 66, when the bank fills them.

## Transfer

| Method                             | Returns                                           |
| ---------------------------------- | ------------------------------------------------- |
| `getHeader(): Header`              | The header (`31`)                                 |
| `getTransactions(): Transaction[]` | The transactions (`34`), in the order of the file |
| `getTotal(): Total`                | The total (`39`)                                  |
| `validate(): void`                 | Since 6.6: checks the consistency of the transfer |

`validate()` throws a `ValidationException` when the total is not the sum of the transactions or belongs to another
account: [checking the consistency of a file](/guides/troubleshooting/#checking-the-consistency-of-a-file).

`getHeader()` and `getTotal()` throw a `HeaderUnavailableException` or a `TotalUnavailableException` when it is not
set, which only happens on a `Transfer` built by hand. `setHeader()`, `addTransaction()` and `setTotal()` build the
transfer; the reader calls them.

## Header

A header line (`31`).

| Method                                            | Field                                 | Positions |
| ------------------------------------------------- | ------------------------------------- | --------- |
| `getSequenceNumber(): int`                        | Sequence number                       | 3–8       |
| `getOperationCode(): ?string`                     | Operation code                        | 9–10      |
| `getPrevTransactionFileDate(): DateTimeInterface` | Date of the previous transaction file | 11–16     |
| `getCurrencyIndex(): string`                      | Currency index                        | 17        |
| `getRecipientBankCode1(): string`                 | Bank code                             | 22–26     |
| `getRecipientCounterCode1(): string`              | Counter code                          | 27–31     |
| `getRecipientAccountNumber1(): string`            | Account number                        | 32–42     |
| `getRecipientName1(): ?string`                    | Name                                  | 43–66     |
| `getRecipientBankCode2(): string`                 | Bank code                             | 78–82     |
| `getRecipientCounterCode2(): string`              | Counter code                          | 83–87     |
| `getRecipientAccountNumber2(): string`            | Account number                        | 88–98     |
| `getRecipientName2(): ?string`                    | Name                                  | 99–122    |
| `getProcessingCenterCode(): ?string`              | Processing center code                | 123–128   |

## Transaction

A transaction line (`34`).

| Method                                                      | Field                                                                        | Positions |
| ----------------------------------------------------------- | ---------------------------------------------------------------------------- | --------- |
| `getSequenceNumber(): int`                                  | Sequence number                                                              | 3–8       |
| `getOperationCode(): ?string`                               | Operation code                                                               | 9–10      |
| `getSettlementDate(): DateTimeInterface`                    | Settlement date                                                              | 11–16     |
| `getCurIndex(): string`                                     | Currency index                                                               | 17        |
| `getRecipientBankCode1(): string`                           | Bank code                                                                    | 22–26     |
| `getRecipientCounterCode1(): string`                        | Counter code                                                                 | 27–31     |
| `getRecipientAccountNumber1(): ?string`                     | Account number                                                               | 32–42     |
| `getRecipientName1(): ?string`                              | Name                                                                         | 43–66     |
| `getNationalIssuerNumber(): ?string`                        | National issuer number                                                       | 67–72     |
| `getRecipientBankCode2(): string`                           | Bank code                                                                    | 78–82     |
| `getRecipientCounterCode2(): string`                        | Counter code                                                                 | 83–87     |
| `getRecipientAccountNumber2(): string`                      | Account number                                                               | 88–98     |
| `getRecipientName2(): ?string`                              | Name                                                                         | 99–122    |
| `getPresenterReference(): ?string`                          | Presenter reference                                                          | 123–128   |
| `getDescription(): ?string`                                 | Description                                                                  | 129–216   |
| `getInitialTransactionSettlementDate(): ?DateTimeInterface` | Settlement date of the initial transaction, `null` when blank or zero-filled | 217–222   |
| `getInitialOperationPresenterReference(): ?string`          | Presenter reference of the initial operation                                 | 223–228   |
| `getTransactionAmount(): float`                             | Amount, in cents in the file                                                 | 229–240   |
| `getTransactionAmountInMinorUnits(): int`                   | Amount in cents, exact: since 6.6                                            | 229–240   |

## Total

A total line (`39`).

| Method                                                | Field                                 | Positions |
| ----------------------------------------------------- | ------------------------------------- | --------- |
| `getSequenceNumber(): int`                            | Sequence number                       | 3–8       |
| `getOperationCode(): ?string`                         | Operation code                        | 9–10      |
| `getCreationTransactionFileDate(): DateTimeInterface` | Creation date of the transaction file | 11–16     |
| `getCurrencyIndex(): string`                          | Currency index                        | 17        |
| `getRecipientBankCode1(): string`                     | Bank code                             | 22–26     |
| `getRecipientCounterCode1(): string`                  | Counter code                          | 27–31     |
| `getRecipientAccountNumber1(): string`                | Account number                        | 32–42     |
| `getRecipientName1(): ?string`                        | Name                                  | 43–66     |
| `getRecipientBankCode2(): string`                     | Bank code                             | 78–82     |
| `getRecipientCounterCode2(): string`                  | Counter code                          | 83–87     |
| `getRecipientAccountNumber2(): string`                | Account number                        | 88–98     |
| `getRecipientName2(): ?string`                        | Name                                  | 99–122    |
| `getProcessingCenterCode(): ?string`                  | Processing center code                | 123–128   |
| `getTotalAmount(): float`                             | Total amount, in cents in the file    | 229–240   |
| `getTotalAmountInMinorUnits(): int`                   | Exact total in cents: since 6.6       | 229–240   |

---
title: Readers
description: Cfonb120Reader, Cfonb240Reader and CfonbReader, their methods and constants, and CfonbFormat.
sidebar:
    order: 1
---

The three readers are `final readonly` classes of the `Silarhi\Cfonb` namespace, without state: one instance can parse
any number of files.

## Cfonb120Reader

```php
use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();
$statements = $reader->parse($content);
```

| Member                                               | Description                                                                                                   |
| ---------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `parse(string $content, bool $strict = true): array` | Parses the contents of a CFONB 120 file into a list of [`Statement`](/reference/cfonb-120-objects/#statement) |
| `LINE_LENGTH`                                        | `120`, the length of a line                                                                                   |

`$strict = false` turns blank operation references into empty strings instead of errors:
[strict mode](/guides/cfonb-120/#strict-mode-default-vs-non-strict-mode).

## Cfonb240Reader

```php
use Silarhi\Cfonb\Cfonb240Reader;

$reader = new Cfonb240Reader();
$transfers = $reader->parse($content);
```

| Member                                               | Description                                                                                                 |
| ---------------------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| `parse(string $content, bool $strict = true): array` | Parses the contents of a CFONB 240 file into a list of [`Transfer`](/reference/cfonb-240-objects/#transfer) |
| `LINE_LENGTH`                                        | `240`, the length of a line                                                                                 |

`$strict` has no effect on CFONB 240 files.

## CfonbReader

Both formats behind one object, e.g. a single service to inject:

```php
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\CfonbReader;

$reader = new CfonbReader();

foreach ($reader->parseCfonb120('My Content') as $statement) {
    assert($statement instanceof Statement);
}

foreach ($reader->parseCfonb240('My Content') as $transfer) {
    assert($transfer instanceof Transfer);
}
```

| Member                                                                                        | Description                                         |
| --------------------------------------------------------------------------------------------- | --------------------------------------------------- |
| `__construct(Cfonb120Reader $cfonb120Reader = new …, Cfonb240Reader $cfonb240Reader = new …)` | The readers to delegate to                          |
| `parse(string $content, bool $strict = true): array`                                          | Since 6.7: either reader, by the format of the file |
| `parseCfonb120(string $content, bool $strict = true): array`                                  | `Cfonb120Reader::parse()`                           |
| `parseCfonb240(string $content, bool $strict = true): array`                                  | `Cfonb240Reader::parse()`                           |

`parse()` detects the format with `CfonbFormat::fromContent()`: the statements of a CFONB 120 file, the transfers of a
CFONB 240 file, an empty list for an empty file, and a `ParseException` for a first record code of neither format.

## CfonbFormat

Since 6.7, the `Silarhi\Cfonb\CfonbFormat` enum tells the two formats apart, by the record code of the first line that
is not empty: `01`, `04`, `05` and `07` are CFONB 120 records, `31`, `34` and `39` CFONB 240 records.

| Member                                          | Description                                                      |
| ----------------------------------------------- | ---------------------------------------------------------------- |
| `Cfonb120`, `Cfonb240`                          | The two formats                                                  |
| `fromContent(string $content): CfonbFormat`     | The format of the content, or a `ParseException`                 |
| `tryFromContent(string $content): ?CfonbFormat` | The format of the content, `null` when it is empty or of neither |
| `lineLength(): int`                             | `120` or `240`                                                   |

## Input

- `$content` is the contents of the file, not its path: read it with `file_get_contents()` or from an upload.
- Lines are separated by `\n`, `\r\n` or `\r` (`\r` since 6.5); a file without line breaks is split every `LINE_LENGTH`
  characters. Empty lines, and line breaks at the start and the end, are ignored; a line of spaces or tabs is not, and
  throws a `ParseException`.
- The characters are kept as they are: no encoding conversion, only the whitespace around each field is trimmed.
- An empty string gives an empty list.

The readers accept an optional `FileParser` in their constructor: it is internal (`Silarhi\Cfonb\Parser`), and not
meant to be replaced.

## Errors

`parse()` throws a [`ParseException`](/reference/exceptions/) on the first line it cannot parse: nothing is returned for
the lines before it.

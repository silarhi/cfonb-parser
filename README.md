<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/v/silarhi/cfonb-parser?style=for-the-badge&label=stable&color=0d6efd&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/v/silarhi/cfonb-parser?style=for-the-badge&label=stable&color=0d6efd"
            alt="Latest Stable Version">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/dt/silarhi/cfonb-parser?style=for-the-badge&color=198754&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/dt/silarhi/cfonb-parser?style=for-the-badge&color=198754" alt="Total Downloads">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/l/silarhi/cfonb-parser?style=for-the-badge&color=6f42c1&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/l/silarhi/cfonb-parser?style=for-the-badge&color=6f42c1" alt="License">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/packagist/php-v/silarhi/cfonb-parser?style=for-the-badge&color=777bb4&labelColor=1a1a2e">
        <img src="https://img.shields.io/packagist/php-v/silarhi/cfonb-parser?style=for-the-badge&color=777bb4" alt="PHP Version">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/github/actions/workflow/status/silarhi/cfonb-parser/continuous-integration.yml?style=for-the-badge&label=CI&color=20c997&branch=6.x&labelColor=1a1a2e">
        <img src="https://img.shields.io/github/actions/workflow/status/silarhi/cfonb-parser/continuous-integration.yml?style=for-the-badge&label=CI&color=20c997&branch=6.x"
            alt="CI Status">
    </picture>
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fsilarhi%2Fcfonb-parser%2Fbadges%2Fcoverage.json&style=for-the-badge&labelColor=1a1a2e">
        <img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fsilarhi%2Fcfonb-parser%2Fbadges%2Fcoverage.json&style=for-the-badge" alt="Coverage">
    </picture>
</p>

<h1 align="center">CFONB Parser</h1>

<p align="center">
    <strong>Parse French CFONB 120 and 240 bank files into typed PHP objects.</strong><br>
    Bank statements and transfers, with zero runtime dependencies.
</p>

<p align="center">
    📖 <a href="https://cfonb-parser.silarhi.dev"><strong>Documentation</strong></a>
</p>

---

## Features

- **Zero dependencies** — only PHP is required, nothing else gets pulled into your project.
- **CFONB 120 statements** — `Statement` objects with opening/closing balances (`01`/`07`), operations (`04`) and their details (`05`).
- **CFONB 240 transfers** — `Transfer` objects with a header (`31`), transactions (`34`) and a total (`39`).
- **Signed amounts** — CFONB 120 decimals and sign characters are decoded: credits are positive, debits are negative.
- **Exact amounts** — since 6.6, every amount is also an exact decimal string (`getAmount(true)`, `"-22.79"`), to store and add without float errors.
- **Consistency checks** — since 6.6, `validate()` checks that a statement's operations belong to its account and add up to its closing balance, and that a transfer's transactions add up to its total.
- **Typed value objects** — getters return floats, dates (`DateTimeImmutable` for CFONB 120, typed `DateTimeInterface` for CFONB 240) and nullable strings, not raw arrays.
- **Tolerant input** — handles `\n`, `\r\n` and `\r` (since 6.5) line endings, and files with no line breaks at all.
- **Strict parsing** — malformed lines and, since 6.5, records out of order and zero-filled dates throw a `ParseException` instead of producing silent garbage, whatever the mode: non-strict mode only accepts blank CFONB 120 operation references. Since 6.6, its message keeps the line (account numbers, labels) out: `getRawContent()` returns it.

## Requirements

- PHP 8.2+

## Installation

```bash
composer require silarhi/cfonb-parser
```

## Quick start

```php
use Silarhi\Cfonb\Cfonb120Reader;

$reader = new Cfonb120Reader();

foreach ($reader->parse(file_get_contents('statements.txt')) as $statement) {
    echo sprintf("Opening balance: %.2f\n", $statement->getOldBalance()->getAmount());

    foreach ($statement->getOperations() as $operation) {
        echo sprintf("%s %s %.2f\n", $operation->getDate()->format('Y-m-d'), $operation->getLabel(), $operation->getAmount());
    }

    echo sprintf("Closing balance: %.2f\n", $statement->getNewBalance()->getAmount());
}
```

CFONB 240 transfers are read the same way with `Cfonb240Reader`, both formats with `CfonbReader`.

## Documentation

Everything else is on **[cfonb-parser.silarhi.dev](https://cfonb-parser.silarhi.dev)**:

- [Installation](https://cfonb-parser.silarhi.dev/getting-started/installation/) and [quick start](https://cfonb-parser.silarhi.dev/getting-started/quick-start/)
- Guides: [CFONB 120 statements](https://cfonb-parser.silarhi.dev/guides/cfonb-120/), [CFONB 240 transfers](https://cfonb-parser.silarhi.dev/guides/cfonb-240/), [recipes](https://cfonb-parser.silarhi.dev/guides/recipes/), [troubleshooting](https://cfonb-parser.silarhi.dev/guides/troubleshooting/)
- Reference: [readers](https://cfonb-parser.silarhi.dev/reference/readers/), [CFONB 120 objects](https://cfonb-parser.silarhi.dev/reference/cfonb-120-objects/), [CFONB 240 objects](https://cfonb-parser.silarhi.dev/reference/cfonb-240-objects/), [operation codes](https://cfonb-parser.silarhi.dev/reference/operation-codes/), [exceptions](https://cfonb-parser.silarhi.dev/reference/exceptions/)

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

MIT License. See [LICENSE](LICENSE) for details.

---

<p align="center">
    Built with care by <a href="https://github.com/silarhi">SILARHI</a>.<br>
    If CFONB Parser saves you time, consider giving it a star on GitHub.
</p>

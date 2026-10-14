# Contributing

Contributions are welcome! Bug reports and feature requests go to the
[issue tracker](https://github.com/silarhi/cfonb-parser/issues), changes to a pull request against `6.x`, the branch of
the current major version.

## Setup

```bash
# Install dependencies
composer install
pnpm install # Git hooks: lint-staged formats the staged files on commit
```

## Quality checks

Please make sure your changes pass them before submitting a pull request. The CI runs PHPUnit on every supported PHP
version, with the lowest and the latest dependencies, and the other checks once (PHPStan, Psalm, PHP CS Fixer,
`composer validate` and `composer normalize`, the documentation examples). Rector is not run by the CI: run it locally.

```bash
# Run tests
vendor/bin/phpunit

# Static analysis (PHPStan level: max)
vendor/bin/phpstan analyse

# Static analysis (Psalm)
vendor/bin/psalm

# Code style check
vendor/bin/php-cs-fixer fix --dry-run --diff

# Code style fix
vendor/bin/php-cs-fixer fix

# Code modernization check
vendor/bin/rector process --dry-run

# composer.json
composer validate --strict
composer normalize --dry-run

# Documentation examples
php docs/scripts/render-examples.php --check
```

All at once:

```bash
vendor/bin/phpunit && vendor/bin/phpstan analyse && vendor/bin/psalm && vendor/bin/php-cs-fixer fix --dry-run --diff \
    && vendor/bin/rector process --dry-run && composer validate --strict && composer normalize --dry-run \
    && php docs/scripts/render-examples.php --check
```

## Documentation

The documentation site, [cfonb-parser.silarhi.dev](https://cfonb-parser.silarhi.dev), is built from the
[`docs/`](docs) folder with [Starlight](https://starlight.astro.build). Document a change in the same pull request:

```bash
cd docs
pnpm install
pnpm dev # http://localhost:4321
```

The examples of the pages are real: `docs/examples/*.php` parse the sample files of `docs/examples/`, and
`docs/scripts/render-examples.php` runs them and writes what they print to `docs/src/data/examples/` (and the operation
codes of `CfonbCodes` to `docs/src/data/operation-codes.json`). After changing an example or the parser, run it and
commit the result; the CI fails when they are out of date:

```bash
php docs/scripts/render-examples.php
```

Pull requests touching `docs/` build the site; it is published once merged into `6.x`.

## License

By contributing, you agree that your contributions are released under the [MIT License](LICENSE).

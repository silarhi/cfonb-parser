---
title: Installation
description: Install the CFONB Parser in a PHP project.
sidebar:
    order: 1
---

## Requirements

- PHP 8.2+

The library has no other runtime dependency: no extension, no framework.

## Installation

```bash
composer require silarhi/cfonb-parser
```

## What is in the package

| Class                                     | Reads                                                             |
| ----------------------------------------- | ----------------------------------------------------------------- |
| `Silarhi\Cfonb\Cfonb120Reader`            | CFONB 120 bank statements (_relevés de compte_), into `Statement` |
| `Silarhi\Cfonb\Cfonb240Reader`            | CFONB 240 transfer files, into `Transfer`                         |
| `Silarhi\Cfonb\CfonbReader`               | Both formats, through the two readers above                       |
| `Silarhi\Cfonb\CfonbCodes`                | The labels of the CFONB operation codes                           |
| `Silarhi\Cfonb\Exceptions\ParseException` | Thrown on a line the readers cannot parse                         |

The readers are plain objects with no state: create them with `new`, or register them as services of your
framework. Everything under `Silarhi\Cfonb\Parser` is internal.

Next: [parse your first file](/getting-started/quick-start/).

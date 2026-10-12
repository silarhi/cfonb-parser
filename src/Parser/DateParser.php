<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) SILARHI <dev@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Parser;

use DateTimeImmutable;
use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;

/** @internal */
final class DateParser
{
    /** Some banks write a date that is not known (e.g. a value date) as zeros. */
    private const ZERO_FILLED = '000000';

    public function parse(string $date): DateTimeImmutable
    {
        if (self::ZERO_FILLED === $date) {
            throw new ParseException(sprintf('Unable to parse date "%s": the date is zero-filled (empty), but this field is mandatory', $date));
        }

        $datetime = DateTimeImmutable::createFromFormat('!dmy', $date);
        // createFromFormat() overflows out-of-range values (day 32 becomes the 1st or 2nd of the next month):
        // the round-trip rejects them.
        if (false === $datetime || $datetime->format('dmy') !== $date) {
            throw new ParseException(sprintf('Unable to parse date "%s"', $date));
        }

        return $datetime;
    }

    /** Parses an optional date: a blank or zero-filled date is null. */
    public function parseOrNull(?string $date): ?DateTimeImmutable
    {
        if (null === $date || self::ZERO_FILLED === $date) {
            return null;
        }

        return $this->parse($date);
    }
}

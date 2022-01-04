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

/** @internal  */
final class DateParser
{
    public function parse(string $date): DateTimeImmutable
    {
        $datetime = DateTimeImmutable::createFromFormat('dmy', $date);
        if (false === $datetime) {
            throw new ParseException(sprintf('Unable to parse date "%s"', $date));
        }

        return $datetime->setTime(0, 0);
    }
}

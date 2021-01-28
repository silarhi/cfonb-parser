<?php

declare(strict_types=1);

namespace Silarhi\Cfonb\Parser;

use DateTimeImmutable;
use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;

class DateParser
{
    public function parse(string $date): DateTimeImmutable
    {
        $datetime = DateTimeImmutable::createFromFormat('dmy', $date);
        if ($datetime === false) {
            throw new ParseException(sprintf('Unable to parse date "%s"', $date));
        }

        return $datetime->setTime(0, 0);
    }
}

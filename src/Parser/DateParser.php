<?php


namespace Silarhi\Cfonb\Parser;


use Silarhi\Cfonb\Exceptions\ParseException;

class DateParser
{
    public function parse(string $date): \DateTimeImmutable
    {
        $datetime = \DateTimeImmutable::createFromFormat('dmy', $date);
        if (false === $datetime) {
            throw new ParseException(sprintf('Unable to parse date "%s"', $date));
        }

        return $datetime->setTime(0, 0);
    }
}
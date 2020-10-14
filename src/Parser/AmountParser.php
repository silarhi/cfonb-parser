<?php


namespace Silarhi\Cfonb\Parser;


use Silarhi\Cfonb\Exceptions\ParseException;

class AmountParser
{
    /** @var string[] */
    private const CREDIT_MAPPING = [
        'A' => '1',
        'B' => '2',
        'C' => '3',
        'D' => '4',
        'E' => '5',
        'F' => '6',
        'G' => '7',
        'H' => '8',
        'I' => '9',
        '{' => '0',
    ];
    /** @var string[] */
    private const DEBIT_MAPPING = [
        'J' => '1',
        'K' => '2',
        'L' => '3',
        'M' => '4',
        'N' => '5',
        'O' => '6',
        'P' => '7',
        'Q' => '8',
        'R' => '9',
        '}' => '0',

    ];

    public function parse(string $content, int $nbDecimals): float
    {
        $content = substr($content, 0, \strlen($content) - $nbDecimals) . '.' . substr($content, -1 * $nbDecimals);
        $lastChar = substr($content, -1);
        if (isset(self::CREDIT_MAPPING[$lastChar])) {
            return (float) (str_replace($lastChar, self::CREDIT_MAPPING[$lastChar], $content));
        }

        if (isset(self::DEBIT_MAPPING[$lastChar])) {
            return -1.0 * (float) (str_replace($lastChar, self::DEBIT_MAPPING[$lastChar], $content));
        }

        throw new ParseException(sprintf('Unable to parse amount "%s"', $content));
    }
}
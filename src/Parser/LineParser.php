<?php

declare(strict_types=1);

namespace Silarhi\Cfonb\Parser;

use Silarhi\Cfonb\Exceptions\ParseException;

use function array_keys;
use function implode;
use function is_array;
use function preg_match;
use function sprintf;
use function strlen;
use function trim;

class LineParser
{
    public const BLANK = '( {%d})';

    public const NUMERIC       = '(\d{%d})';
    public const NUMERIC_BLANK = '([0-9 ]{%d})';

    public const ALPHA       = '(\w{%d})';
    public const ALPHA_BLANK = '([a-zA-Z ]{%d})';

    public const ALPHANUMERIC       = '([a-zA-Z0-9]{%d})';
    public const ALPHANUMERIC_BLANK = '([a-zA-Z0-9 ]{%d})';

    public const AMOUNT = '(\d{%d}[{}A-R]{1})';

    public const ALL = '(.{%d})';

    public function parse(string $content, array $parts): array
    {
        $regexParts = [];
        foreach ($parts as $part) {
            $regexParts[] = is_array($part) ? sprintf($part[0], $part[1]) : $part;
        }

        $regex = sprintf('/^%s$/', implode('', $regexParts));

        if (! preg_match($regex, $content, $matches)) {
            throw new ParseException(sprintf('Regex does not match the line'));
        }

        $values = [];
        foreach (array_keys($parts) as $i => $key) {
            $value        = trim($matches[$i + 1]);
            $values[$key] = strlen($value) !== 0 ? $value : null;
        }

        return $values;
    }
}

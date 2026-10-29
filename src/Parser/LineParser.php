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

use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;
use function strlen;

/** @internal */
final class LineParser
{
    public const BLANK = '( {%d})';

    public const NUMERIC = '(\d{%d})';
    public const NUMERIC_BLANK = '([0-9 ]{%d})';

    public const ALPHA = '(\w{%d})';
    public const ALPHA_BLANK = '([a-zA-Z ]{%d})';

    public const ALPHANUMERIC = '([a-zA-Z0-9]{%d})';
    public const ALPHANUMERIC_BLANK = '([a-zA-Z0-9 ]{%d})';

    public const AMOUNT = '(\d{%d}[{}A-R]{1})';

    public const ALL = '(.{%d})';

    /** @var non-empty-string */
    private readonly string $regexAsString;

    /** @var list<string> */
    private readonly array $matchingKeys;

    /** @param array<string, RegexParts> $regexParts */
    public function __construct(private array $regexParts)
    {
        $regexParts = [];
        $matchingKeys = [];
        foreach ($this->regexParts as $key => $part) {
            $regexParts[] = $part->toString();
            if ($part->isMatching()) {
                $matchingKeys[] = $key;
            }
        }
        $this->matchingKeys = $matchingKeys;

        $this->regexAsString = '/^' . implode('', $regexParts) . '$/';
    }

    public function parse(string $content): RegexMatch
    {
        if (1 !== preg_match($this->regexAsString, $content, $matches)) {
            // The line holds account numbers and labels: the message only names the field that does not match.
            throw new ParseException(sprintf('Regex does not match the line: %s', $this->describeMismatch($content)), rawContent: $content);
        }

        return RegexMatch::fromMatchingKeys($this->matchingKeys, $matches);
    }

    /** Finds the first field that does not match, by matching the parts one more at a time: only run on failure. */
    private function describeMismatch(string $content): string
    {
        $regex = '';
        $length = 0;
        foreach ($this->regexParts as $key => $part) {
            $regex .= $part->toString();
            if (1 !== preg_match('/^' . $regex . '/', $content, $matches)) {
                return sprintf('the field "%s" at position %d is invalid', $key, $length + 1);
            }
            $length = strlen($matches[0]);
        }

        return sprintf('unexpected characters after position %d', $length);
    }
}

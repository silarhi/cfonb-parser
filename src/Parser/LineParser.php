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

/** @internal  */
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
    private string $regexAsString;

    /** @param array<string, RegexParts> $regexParts */
    public function __construct(private array $regexParts)
    {
        $regexParts = [];
        foreach ($this->regexParts as $part) {
            $regexParts[] = $part->toString();
        }

        $this->regexAsString = implode('', $regexParts);
    }

    public function parse(string $content): RegexMatch
    {
        $regex = sprintf('/^%s$/', $this->regexAsString);

        if (!preg_match($regex, $content, $matches)) {
            throw new ParseException(sprintf('Regex does not match the line "%s"', $content));
        }

        return new RegexMatch($this->regexParts, $matches);
    }
}

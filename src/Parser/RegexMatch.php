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

use function array_key_exists;
use function is_string;

use Silarhi\Cfonb\Exceptions\KeyDoesNotExistException;
use Silarhi\Cfonb\Exceptions\ValueOfKeyIsNotAStringException;
use Silarhi\Cfonb\Exceptions\ValueOfKeyIsNotNumericException;

use function strlen;

/** @internal  */
class RegexMatch
{
    /** @var array<string, string|null> */
    private $values;

    /**
     * @param array<string, RegexParts> $regexParts
     * @param array<array-key, string>  $matches
     */
    public function __construct(array $regexParts, array $matches)
    {
        $values = [];
        $index = 0;

        foreach ($regexParts as $key => $regexPart) {
            if (!$regexPart->isMatching()) {
                continue;
            }
            ++$index;

            if (!array_key_exists($index, $matches)) {
                throw new KeyDoesNotExistException((string) $index);
            }

            $value = trim($matches[$index]);
            $values[$key] = 0 != strlen($value) ? $value : null;
        }

        $this->values = $values;
    }

    public function getString(string $key): string
    {
        $value = $this->getValue($key);

        if (!is_string($value)) {
            throw new ValueOfKeyIsNotAStringException($key);
        }

        return $value;
    }

    public function getInt(string $key): int
    {
        $value = $this->getValue($key);

        if (!is_numeric($value)) {
            throw new ValueOfKeyIsNotNumericException($key);
        }

        return (int) $value;
    }

    public function getFloat(string $key): float
    {
        $value = $this->getValue($key);

        if (!is_numeric($value)) {
            throw new ValueOfKeyIsNotNumericException($key);
        }

        return (float) $value;
    }

    public function getStringOrNull(string $key): ?string
    {
        return $this->getValue($key);
    }

    public function isNull(string $key): bool
    {
        return null === $this->getValue($key);
    }

    private function getValue(string $key): ?string
    {
        if (!array_key_exists($key, $this->values)) {
            throw new KeyDoesNotExistException($key);
        }

        return $this->values[$key];
    }
}

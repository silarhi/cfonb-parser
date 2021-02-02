<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Parser;

/** @internal  */
class RegexParts
{
    /** @var string */
    private $regexParts;
    /** @var int|null */
    private $length;
    /** @var bool */
    private $matching;

    public function __construct(string $regexParts, int $length = null, bool $matching = true)
    {
        $this->regexParts = $regexParts;
        $this->length = $length;
        $this->matching = $matching;
    }

    public function isMatching(): bool
    {
        return $this->matching;
    }

    public function toString(): string
    {
        if (null === $this->length) {
            return $this->regexParts;
        }

        return sprintf($this->regexParts, $this->length);
    }
}

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

use Silarhi\Cfonb\Banking\Noop;
use Silarhi\Cfonb\Contracts\ParserInterface;

/** @internal  */
final class EmptyParser implements ParserInterface
{
    public function parse(string $content): Noop
    {
        return new Noop();
    }

    public function supports(string $content): bool
    {
        return empty($content);
    }
}

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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Parser\AbstractCfonbParser;

use function strlen;
use function substr;

abstract class AbstractCfonb120Parser extends AbstractCfonbParser
{
    public function supports(string $content): bool
    {
        return strlen($content) === 120 && $this->getSupportedCode() === substr($content, 0, 2);
    }
}

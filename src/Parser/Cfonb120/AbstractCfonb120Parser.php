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

abstract class AbstractCfonb120Parser extends AbstractCfonbParser
{
    /**
     * {@inheritdoc}
     */
    public function supports(string $content): bool
    {
        return 120 === strlen($content) && $this->getSupportedCode() === substr($content, 0, 2);
    }
}

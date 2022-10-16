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

namespace Silarhi\Cfonb\Parser\Cfonb240;

use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\Contracts\ParserInterface;

use function strlen;

/** @internal  */
abstract class AbstractCfonb240Parser implements ParserInterface
{
    abstract protected function getSupportedCode(): string;

    /**
     * {@inheritdoc}
     */
    public function supports(string $content): bool
    {
        return Cfonb240Reader::LINE_LENGTH === strlen($content) && $this->getSupportedCode() === substr($content, 0, 2);
    }
}

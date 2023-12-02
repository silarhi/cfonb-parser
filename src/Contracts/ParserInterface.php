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

namespace Silarhi\Cfonb\Contracts;

use Silarhi\Cfonb\Banking\Element;

interface ParserInterface
{
    /**
     * Parse the current line
     */
    public function parse(string $content, bool $strict): Element;

    /**
     * Checks if current line is handled by the parser
     *
     * @return bool true if the line is handled by the current parser, false otherwise
     */
    public function supports(string $content): bool;
}

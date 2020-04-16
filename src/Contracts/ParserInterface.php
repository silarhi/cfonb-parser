<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts;

use Silarhi\Cfonb\Banking\Balance;

interface ParserInterface
{
    /**
     * Parse the current line
     *
     * @param string $content
     *
     * @return Balance
     */
    public function parse($content);

    /**
     * Checks if current line is handled by the parser
     *
     * @param string $content
     *
     * @return bool true if the line is handled by the current parser, false otherwise
     */
    public function supports($content);
}

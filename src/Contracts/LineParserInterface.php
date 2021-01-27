<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts;

interface LineParserInterface
{
    /**
     * Parse the current line
     *
     * @param string $content
     *
     * @return ReadItemInterface
     */
    public function parse(string $content);

    /**
     * Checks if current line is handled by the parser
     *
     * @param string $line
     *
     * @return bool true if the line is handled by the current parser, false otherwise
     */
    public function supports(string $line): bool;
}

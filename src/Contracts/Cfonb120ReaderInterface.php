<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts;

use Silarhi\Cfonb\Banking\Statement;

/**
 * Interface Cfonb120ReaderInterface specifies general methods for reader,
 * those will not changed for the version.
 */
interface Cfonb120ReaderInterface
{
    /**
     * Parse raw content onto statements.
     *
     * @param string $content
     *
     * @return Statement[] Collection of statements.
     */
    public function parse(string $content): array;
}

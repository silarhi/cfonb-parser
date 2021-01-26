<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts;

/**
 * Interface Cfonb240ReaderInterface specifies general methods for reader,
 * those will not changed for the version.
 */
interface CfonbReaderInterface
{
    /**
     * Parse raw content onto items.
     *
     * @param string $content
     *
     * @return ReadItemInterface[] Collection of read items.
     */
    public function parse(string $content): array;
}

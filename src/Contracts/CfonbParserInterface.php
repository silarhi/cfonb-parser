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
 * Interface CfonbParserInterface specifies general methods for facade,
 * those will not changed for the version.
 */
interface CfonbParserInterface
{

    /**
     * Read content in cfonb.120 format.
     *
     * @param string $content
     *
     * @return array
     */
    public function read120C(string $content): array;

    /**
     * Read content in cfonb.240 format.
     *
     * @param string $content
     *
     * @return array
     */
    public function read240C(string $content): array;
}

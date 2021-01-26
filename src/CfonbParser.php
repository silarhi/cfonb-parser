<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb;

use Silarhi\Cfonb\Contracts\CfonbParserInterface;
use Silarhi\Cfonb\Factories\CfonbReaderFactory;

/**
 * Facade for Cfonb readers.
 */
class CfonbParser implements CfonbParserInterface
{

    /**
     * @var CfonbReaderFactory
     */
    private $cfonbReaderFactory;

    public function __construct()
    {
        $this->cfonbReaderFactory = new CfonbReaderFactory();
    }

    public function read120C(string $content): array
    {
        return $this->cfonbReaderFactory->createCfonb120Reader()->parse($content);
    }

    public function read240C($content): array
    {
        return $this->cfonbReaderFactory->createCfonb240Reader()->parse($content);
    }
}

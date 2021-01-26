<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Factories;

use Silarhi\Cfonb\Contracts\CfonbReaderInterface;
use Silarhi\Cfonb\Services\Cfonb120Reader;
use Silarhi\Cfonb\Services\Cfonb240Reader;

/**
 * Abstract factory allows to use Singleton.
 */
class CfonbReaderFactory
{

    public function createCfonb120Reader(): CfonbReaderInterface
    {
        return new Cfonb120Reader();
    }

    public function createCfonb240Reader(): CfonbReaderInterface
    {
        return new Cfonb240Reader();
    }
}

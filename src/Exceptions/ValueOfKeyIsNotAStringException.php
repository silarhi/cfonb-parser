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

namespace Silarhi\Cfonb\Exceptions;

final class ValueOfKeyIsNotAStringException extends ParseException
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('The value of key "%s" is not a string', $key));
    }
}

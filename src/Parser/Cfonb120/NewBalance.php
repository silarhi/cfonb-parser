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

namespace Silarhi\Cfonb\Parser\Cfonb120;

use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Element;

/**
 * A "07" new balance record: tells it apart from a "01" old balance, both being a {@see Balance}.
 *
 * @internal
 */
final class NewBalance extends Element
{
    public function __construct(public readonly Balance $balance)
    {
    }
}

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

namespace Silarhi\Cfonb\Tests;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Direction;

final class DirectionTest extends TestCase
{
    public function testDirection(): void
    {
        self::assertTrue(Direction::Debit->canDebit());
        self::assertFalse(Direction::Debit->canCredit());
        self::assertFalse(Direction::Credit->canDebit());
        self::assertTrue(Direction::Credit->canCredit());
        self::assertTrue(Direction::DebitOrCredit->canDebit());
        self::assertTrue(Direction::DebitOrCredit->canCredit());
        self::assertTrue(Direction::CreditOrDebit->canDebit());
        self::assertTrue(Direction::CreditOrDebit->canCredit());
    }
}

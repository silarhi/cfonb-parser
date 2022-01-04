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

namespace Siarhi\Cfonb\Tests\Parser;

use Generator;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Parser\RegexParts;

class RegexPartsTest extends TestCase
{
    /** @return Generator<int, array<int, string|int>> */
    public function provideOkCase(): iterable
    {
        yield ['test', 'test'];
        yield ['test(10)', 'test(%d)', 10];
        yield ['test10', 'test%d', 10];
    }

    /**
     * @dataProvider provideOkCase
     *
     * @return void
     */
    public function testOk(string $expected, string $regexParts, ?int $length = null)
    {
        $sUT = new RegexParts($regexParts, $length);

        self::assertSame($expected, $sUT->toString());
    }
}

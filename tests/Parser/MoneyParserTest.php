<?php

declare(strict_types=1);

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Siarhi\Cfonb\Tests\Parser;

use Generator;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Parser\MoneyParser;

class MoneyParserTest extends TestCase
{
    /** @return Generator<int, array<int, float>> */
    public function provideOkCase(): iterable
    {
        yield [100.0, 1.0];
    }

    /**
     * @dataProvider provideOkCase
     *
     * @return void
     */
    public function testOk(float $content, float $expected)
    {
        $sUT = new MoneyParser();

        self::assertSame($expected, $sUT->parser($content));
    }
}

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

namespace Silarhi\Cfonb\Tests\Parser;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Parser\MoneyParser;

class MoneyParserTest extends TestCase
{
    /** @return Generator<int, array<int, float>> */
    public static function provideOkCase(): iterable
    {
        yield [100.0, 1.0];
    }

    /**
     * @return void
     */
    #[DataProvider('provideOkCase')]
    public function testOk(float $content, float $expected)
    {
        $sUT = new MoneyParser();

        self::assertSame($expected, $sUT->parser($content));
    }
}

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
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\AmountParser;

class AmountParserTest extends TestCase
{
    /** @return void */
    public function testFail()
    {
        $sUT = new AmountParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse amount "tes.test"');

        $sUT->parse('test', 5);
    }

    /** @return Generator<int, array<int, string|int|float>> */
    public static function provideOkCase(): iterable
    {
        yield ['10A', 1, 10.1];
        yield ['10B', 1, 10.2];
        yield ['10C', 1, 10.3];
        yield ['10D', 1, 10.4];
        yield ['10E', 1, 10.5];
        yield ['10F', 1, 10.6];
        yield ['10G', 1, 10.7];
        yield ['10H', 1, 10.8];
        yield ['10I', 1, 10.9];
        yield ['10{', 1, 10.0];
        yield ['10.10{', 1, 10.10];
        yield ['10J', 1, -10.1];
        yield ['10K', 1, -10.2];
        yield ['10L', 1, -10.3];
        yield ['10M', 1, -10.4];
        yield ['10N', 1, -10.5];
        yield ['10O', 1, -10.6];
        yield ['10P', 1, -10.7];
        yield ['10Q', 1, -10.8];
        yield ['10R', 1, -10.9];
        yield ['10}', 1, -10.0];
        yield ['10.10}', 1, -10.10];
    }

    /**
     * @return void
     */
    #[DataProvider('provideOkCase')]
    public function testOk(string $content, int $nbDecimal, float $expected)
    {
        $sUT = new AmountParser();

        self::assertSame($expected, $sUT->parse($content, $nbDecimal));
    }
}

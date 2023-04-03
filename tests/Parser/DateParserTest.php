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

use PHPUnit\Framework\Attributes\DataProvider;
use Generator;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\DateParser;

class DateParserTest extends TestCase
{
    /** @return void */
    public function testFail()
    {
        $sUT = new DateParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "test"');

        $sUT->parse('test');
    }

    /** @return Generator<int, array<int, string>> */
    public static function provideOkCase(): iterable
    {
        yield ['101020', '2020-10-10 00:00:00'];
    }

    /**
     * @return void
     */
    #[DataProvider('provideOkCase')]
    public function testOk(string $content, string $expected)
    {
        $sUT = new DateParser();

        self::assertSame($expected, $sUT->parse($content)->format('Y-m-d H:i:s'));
    }
}

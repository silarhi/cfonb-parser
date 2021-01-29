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

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\DateParser;

class DateParserTest extends TestCase
{
    /** @return void */
    public function testFail()
    {
        $sUT = new DateParser();

        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to parse date "test"');

        $sUT->parse('test');
    }

    public function provideOkCase(): iterable
    {
        yield ['101020', '2020-10-10 00:00:00'];
    }

    /**
     * @dataProvider provideOkCase
     *
     * @return void
     */
    public function testOk(string $content, string $expected)
    {
        $sUT = new DateParser();

        self::assertSame($expected, $sUT->parse($content)->format('Y-m-d H:i:s'));
    }
}

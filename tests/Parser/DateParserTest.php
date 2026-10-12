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
use Silarhi\Cfonb\Parser\DateParser;

use function sprintf;

final class DateParserTest extends TestCase
{
    public function testFail(): void
    {
        $sUT = new DateParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "test"');

        $sUT->parse('test');
    }

    /** @return iterable<string, array<string>> */
    public static function provideOutOfRangeCase(): iterable
    {
        yield 'day 32' => ['320420'];
        yield 'day 0' => ['000420'];
        yield 'month 13' => ['011320'];
        yield 'month 0' => ['010020'];
        yield '31 April' => ['310420'];
        yield '29 February of a non-leap year' => ['290221'];
    }

    #[DataProvider('provideOutOfRangeCase')]
    public function testFailOutOfRange(string $content): void
    {
        $sUT = new DateParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage(sprintf('Unable to parse date "%s"', $content));

        $sUT->parse($content);
    }

    public function testFailZeroFilled(): void
    {
        $sUT = new DateParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "000000": the date is zero-filled (empty), but this field is mandatory');

        $sUT->parse('000000');
    }

    public function testParseOrNull(): void
    {
        $sUT = new DateParser();

        self::assertNull($sUT->parseOrNull(null));
        self::assertNull($sUT->parseOrNull('000000'));
        self::assertSame('2020-10-10 00:00:00', $sUT->parseOrNull('101020')?->format('Y-m-d H:i:s'));
    }

    public function testParseOrNullFailOutOfRange(): void
    {
        $sUT = new DateParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "320420"');

        $sUT->parseOrNull('320420');
    }

    /** @return Generator<int, array<int, string>> */
    public static function provideOkCase(): iterable
    {
        yield ['101020', '2020-10-10 00:00:00'];
        yield ['290220', '2020-02-29 00:00:00'];
        yield ['311299', '1999-12-31 00:00:00'];
        yield ['010100', '2000-01-01 00:00:00'];
    }

    #[DataProvider('provideOkCase')]
    public function testOk(string $content, string $expected): void
    {
        $sUT = new DateParser();

        self::assertSame($expected, $sUT->parse($content)->format('Y-m-d H:i:s'));
    }
}

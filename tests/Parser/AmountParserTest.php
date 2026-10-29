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

final class AmountParserTest extends TestCase
{
    public function testFail(): void
    {
        $sUT = new AmountParser();

        try {
            $sUT->parse('test', 5);
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            // Only the sign character in the message: the amount is kept in the exception.
            self::assertSame('Unable to parse amount: its last character "t" is not a sign character', $e->getMessage());
            self::assertSame('test', $e->getRawContent());
        }
    }

    public function testFailInMinorUnits(): void
    {
        $sUT = new AmountParser();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse amount: its last character "t" is not a sign character');

        $sUT->parseInMinorUnits('test');
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
        // No decimals (position 20 of a CFONB 120 line set to 0): the amount is an integer
        yield ['0000000166956E', 0, 1669565.0];
        yield ['0000000166956N', 0, -1669565.0];
        yield ['0000000000000{', 0, 0.0];
        yield ['0000000166956E', 2, 16695.65];
        yield ['0000000166956N', 3, -1669.565];
    }

    #[DataProvider('provideOkCase')]
    public function testOk(string $content, int $nbDecimal, float $expected): void
    {
        $sUT = new AmountParser();

        self::assertSame($expected, $sUT->parse($content, $nbDecimal));
    }

    /** @return iterable<string, array{string, int}> */
    public static function provideMinorUnitsCase(): iterable
    {
        yield 'credit' => ['0000000166956E', 1669565];
        yield 'debit' => ['0000000000227R', -2279];
        yield 'credit zero' => ['0000000000000{', 0];
        yield 'debit zero' => ['0000000000000}', 0];
        // (int) (0.29 * 100) is 28: the float of parse() loses a cent there, not the minor units
        yield '0.29' => ['0000000000002I', 29];
        yield 'debit 0.29' => ['0000000000002R', -29];
        yield 'largest amount' => ['9999999999999I', 99999999999999];
    }

    #[DataProvider('provideMinorUnitsCase')]
    public function testInMinorUnits(string $content, int $expected): void
    {
        $sUT = new AmountParser();

        self::assertSame($expected, $sUT->parseInMinorUnits($content));
    }
}

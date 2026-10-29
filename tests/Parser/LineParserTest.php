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
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\LineParser;
use Silarhi\Cfonb\Parser\RegexParts;

final class LineParserTest extends TestCase
{
    private function createParser(): LineParser
    {
        return new LineParser([
            'record_code' => new RegexParts('01', null, false),
            'account_nb' => new RegexParts(LineParser::NUMERIC, 4),
            '_unused_1' => new RegexParts(LineParser::BLANK, 2),
            'amount' => new RegexParts(LineParser::AMOUNT, 3),
        ]);
    }

    public function testOk(): void
    {
        $regexMatch = $this->createParser()->parse('011234  000A');

        self::assertSame('1234', $regexMatch->getString('account_nb'));
        self::assertSame('000A', $regexMatch->getString('amount'));
    }

    /** @return iterable<string, array{string, string}> */
    public static function provideMismatchCase(): iterable
    {
        yield 'record code' => ['021234  000A', 'Regex does not match the line: the field "record_code" at position 1 is invalid'];
        yield 'letter in a numeric field' => ['0112X4  000A', 'Regex does not match the line: the field "account_nb" at position 3 is invalid'];
        yield 'blank field not blank' => ['011234 X000A', 'Regex does not match the line: the field "_unused_1" at position 7 is invalid'];
        yield 'sign character' => ['011234  000Z', 'Regex does not match the line: the field "amount" at position 9 is invalid'];
        yield 'line too short' => ['011234  000', 'Regex does not match the line: the field "amount" at position 9 is invalid'];
        yield 'line too long' => ['011234  000A9', 'Regex does not match the line: unexpected characters after position 12'];
    }

    #[DataProvider('provideMismatchCase')]
    public function testMismatch(string $content, string $message): void
    {
        try {
            $this->createParser()->parse($content);
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertSame($message, $e->getMessage());
            self::assertStringNotContainsString($content, $e->getMessage());
            self::assertSame($content, $e->getRawContent());
        }
    }
}

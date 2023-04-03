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

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Exceptions\KeyDoesNotExistException;
use Silarhi\Cfonb\Exceptions\ValueOfKeyIsNotAStringException;
use Silarhi\Cfonb\Exceptions\ValueOfKeyIsNotNumericException;
use Silarhi\Cfonb\Parser\RegexMatch;
use Silarhi\Cfonb\Parser\RegexParts;

class RegexMatchTest extends TestCase
{
    public function testKeyDoesNotExistOnGetStringOrNull(): void
    {
        $sUT = new RegexMatch([], []);

        $this->expectException(KeyDoesNotExistException::class);
        $this->expectExceptionMessage('key "test" does not exis');

        $sUT->getStringOrNull('test');
    }

    public function testKeyDoesNotExistOnGetString(): void
    {
        $sUT = new RegexMatch([], []);

        $this->expectException(KeyDoesNotExistException::class);
        $this->expectExceptionMessage('key "test" does not exis');

        $sUT->getString('test');
    }

    public function testKeyDoesNotExistOnGetInt(): void
    {
        $sUT = new RegexMatch([], []);

        $this->expectException(KeyDoesNotExistException::class);
        $this->expectExceptionMessage('key "test" does not exist');

        $sUT->getInt('test');
    }

    public function testFailOnConstruct(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $this->expectException(KeyDoesNotExistException::class);
        $this->expectExceptionMessage('key "1" does not exist');

        new RegexMatch(['test' => $regexParts], ['']);
    }

    public function testGetStringOrNullIsNull(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => '']);

        self::assertNull($sUT->getStringOrNull('test'));

        $this->expectException(ValueOfKeyIsNotAStringException::class);
        $this->expectExceptionMessage('The value of key "test" is not a string');

        $sUT->getString('test');
    }

    public function testGetStringOk(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => 'toto']);

        self::assertSame('toto', $sUT->getStringOrNull('test'));
        self::assertSame('toto', $sUT->getString('test'));
    }

    public function testGetIntNotNumeric(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => 'test']);

        $this->expectException(ValueOfKeyIsNotNumericException::class);
        $this->expectExceptionMessage('The value of key "test" is not numeric');

        $sUT->getInt('test');
    }

    public function testGetIntOk(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => '10']);

        self::assertSame(10, $sUT->getInt('test'));
    }

    public function testGetFloatNotNumeric(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => 'test']);

        $this->expectException(ValueOfKeyIsNotNumericException::class);
        $this->expectExceptionMessage('The value of key "test" is not numeric');

        $sUT->getFloat('test');
    }

    public function testGetFloatOk(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => '10']);

        self::assertSame(10.0, $sUT->getFloat('test'));
    }

    public function testIsNull(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::exactly(2))->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts, 'test2' => $regexParts], [1 => '', 2 => 'toto']);

        self::assertTrue($sUT->isNull('test'));
        self::assertFalse($sUT->isNull('test2'));

        $this->expectException(KeyDoesNotExistException::class);
        $this->expectExceptionMessage('key "test3" does not exist');

        $sUT->isNull('test3');
    }
}

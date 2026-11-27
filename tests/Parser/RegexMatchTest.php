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

final class RegexMatchTest extends TestCase
{
    public function testKeyDoesNotExistOnGetStringOrNull(): void
    {
        $sUT = new RegexMatch([], []);

        try {
            $sUT->getStringOrNull('test');
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "test" does not exis', $e->getMessage());
        }
    }

    public function testKeyDoesNotExistOnGetString(): void
    {
        $sUT = new RegexMatch([], []);

        try {
            $sUT->getString('test');
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "test" does not exis', $e->getMessage());
        }
    }

    public function testKeyDoesNotExistOnGetInt(): void
    {
        $sUT = new RegexMatch([], []);

        try {
            $sUT->getInt('test');
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "test" does not exist', $e->getMessage());
        }
    }

    public function testFailOnConstruct(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        try {
            new RegexMatch(['test' => $regexParts], ['']);
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "1" does not exist', $e->getMessage());
        }
    }

    public function testNonMatchingPartsAreSkipped(): void
    {
        $sUT = new RegexMatch([
            'bank' => new RegexParts('(\d{5})'),
            'reserved' => new RegexParts('.{2}', null, false),
            'account' => new RegexParts('(.{11})'),
        ], [1 => '20041', 2 => ' 0123456789A ']);

        // The non capturing part does not consume a match: the next part gets the 2nd one
        self::assertSame('20041', $sUT->getString('bank'));
        self::assertSame('0123456789A', $sUT->getString('account'));

        try {
            $sUT->getStringOrNull('reserved');
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "reserved" does not exist', $e->getMessage());
        }
    }

    public function testGetStringOrNullIsNull(): void
    {
        $regexParts = $this->createMock(RegexParts::class);
        $regexParts->expects(self::once())->method('isMatching')->willReturn(true);

        $sUT = new RegexMatch(['test' => $regexParts], [1 => '']);

        self::assertNull($sUT->getStringOrNull('test'));

        try {
            $sUT->getString('test');
            self::fail('A ValueOfKeyIsNotAStringException should be thrown');
        } catch (ValueOfKeyIsNotAStringException $e) {
            self::assertStringContainsString('The value of key "test" is not a string', $e->getMessage());
        }
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

        try {
            $sUT->getInt('test');
            self::fail('A ValueOfKeyIsNotNumericException should be thrown');
        } catch (ValueOfKeyIsNotNumericException $e) {
            self::assertStringContainsString('The value of key "test" is not numeric', $e->getMessage());
        }
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

        try {
            $sUT->getFloat('test');
            self::fail('A ValueOfKeyIsNotNumericException should be thrown');
        } catch (ValueOfKeyIsNotNumericException $e) {
            self::assertStringContainsString('The value of key "test" is not numeric', $e->getMessage());
        }
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

        try {
            $sUT->isNull('test3');
            self::fail('A KeyDoesNotExistException should be thrown');
        } catch (KeyDoesNotExistException $e) {
            self::assertStringContainsString('key "test3" does not exist', $e->getMessage());
        }
    }
}

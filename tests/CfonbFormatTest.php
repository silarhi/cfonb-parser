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

namespace Silarhi\Cfonb\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Silarhi\Cfonb\CfonbFormat;
use Silarhi\Cfonb\Exceptions\ParseException;

final class CfonbFormatTest extends CfonbTestCase
{
    /** @return iterable<string, array{string, CfonbFormat}> */
    public static function provideFormatCase(): iterable
    {
        yield 'CFONB 120' => [self::loadFixture('complex-test.txt', false), CfonbFormat::Cfonb120];
        yield 'CFONB 120 without line breaks' => [self::loadFixture('complex-test.txt', true), CfonbFormat::Cfonb120];
        yield 'CFONB 240' => [self::loadFixture('cfonb.240-complex-test.txt', false), CfonbFormat::Cfonb240];
        yield 'CFONB 240 after empty lines' => ["\r\n\r\n" . self::loadFixture('cfonb.240-complex-test.txt', false), CfonbFormat::Cfonb240];
        yield 'CFONB 120 starting with an operation' => ['04' . str_repeat(' ', 118), CfonbFormat::Cfonb120];
    }

    #[DataProvider('provideFormatCase')]
    public function testFromContent(string $content, CfonbFormat $format): void
    {
        self::assertSame($format, CfonbFormat::fromContent($content));
        self::assertSame($format, CfonbFormat::tryFromContent($content));
    }

    public function testLineLength(): void
    {
        self::assertSame(120, CfonbFormat::Cfonb120->lineLength());
        self::assertSame(240, CfonbFormat::Cfonb240->lineLength());
    }

    public function testEmpty(): void
    {
        self::assertNull(CfonbFormat::tryFromContent("\n\n"));

        try {
            CfonbFormat::fromContent("\r\n");
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertStringContainsString('Unable to detect the CFONB format: the content is empty', $e->getMessage());
        }
    }

    public function testUnknownRecordCode(): void
    {
        $line = '0310278    02204EUR2 00012345603';
        self::assertNull(CfonbFormat::tryFromContent($line));

        try {
            CfonbFormat::fromContent($line . "\n");
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertSame('Unable to detect the CFONB format: the record code "03" of the first line is neither a CFONB 120 record (01, 04, 05, 07) nor a CFONB 240 record (31, 34, 39)', $e->getMessage());
            self::assertSame($line, $e->getRawContent());
        }
    }
}

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
use Silarhi\Cfonb\Banking\Element;
use Silarhi\Cfonb\Banking\Noop;
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Parser\FileParser;

final class FileParserTest extends TestCase
{
    /** @return iterable<string, array<string>> */
    public static function provideEmptyCase(): iterable
    {
        yield 'empty string' => [''];
        yield 'lf' => ["\n"];
        yield 'crlf' => ["\r\n"];
    }

    #[DataProvider('provideEmptyCase')]
    public function testEmpty(string $content): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::never())->method('supports');

        $sUT = new FileParser($parser);

        self::assertSame([], iterator_to_array($sUT->parse($content, 10, true)));
    }

    /** @return iterable<string, array<string, mixed>> */
    public static function provideComplexCase(): iterable
    {
        $object1 = new Noop();
        $object2 = new Noop();
        $object3 = new Noop();

        yield 'with split' => [
            'invokedCountSupport' => 3,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
                2 => ['bbbbbbbbbb'],
                3 => [''],
            ],
            'supportReturnValue' => [
                1 => true,
                2 => true,
                3 => true,
            ],
            'invokedCountParse' => 3,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
                2 => 'bbbbbbbbbb',
                3 => '',
            ],
            'parseReturnValue' => [
                1 => $object1,
                2 => $object2,
                3 => $object3,
            ],
            'expected' => [$object1, $object2, $object3],
            'content' => 'aaaaaaaaaabbbbbbbbbb',
            'lineLength' => 10,
            'strict' => true,
        ];

        $object1 = new Noop();

        yield 'simple' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => 'aaaaaaaaaa',
            'lineLength' => 10,
            'strict' => true,
        ];

        $object1 = new Noop();

        yield 'with crlf at beginning and end' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => "\r\naaaaaaaaaa\r\n",
            'lineLength' => 10,
            'strict' => true,
        ];

        yield 'with crlf at end' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => "aaaaaaaaaa\r\n",
            'lineLength' => 10,
            'strict' => true,
        ];

        yield 'with lf at beginning and end' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => "\naaaaaaaaaa\n",
            'lineLength' => 10,
            'strict' => true,
        ];

        yield 'with lf at end' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['aaaaaaaaaa'],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => 'aaaaaaaaaa',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => "aaaaaaaaaa\n",
            'lineLength' => 10,
            'strict' => true,
        ];

        yield 'with lf at end and empty string' => [
            'invokedCountSupport' => 1,
            'supportArgs' => [
                1 => ['      '],
            ],
            'supportReturnValue' => [
                1 => true,
            ],
            'invokedCountParse' => 1,
            'parseArgs' => [
                1 => '      ',
            ],
            'parseReturnValue' => [
                1 => $object1,
            ],
            'expected' => [$object1],
            'content' => "      \n",
            'lineLength' => 10,
            'strict' => true,
        ];
    }

    /**
     * @param array<int, array<string>> $supportArgs
     * @param array<int, bool>          $supportReturnValue
     * @param array<int, string>        $parseArgs
     * @param array<int, Element>       $parseReturnValue
     * @param array<Element>            $expected
     * @param positive-int              $lineLength
     */
    #[DataProvider('provideComplexCase')]
    public function testSplitOk(
        int $invokedCountSupport,
        array $supportArgs,
        array $supportReturnValue,
        int $invokedCountParse,
        array $parseArgs,
        array $parseReturnValue,
        array $expected,
        string $content,
        int $lineLength,
        bool $strict,
    ): void {
        $parser = $this->createMock(ParserInterface::class);

        $invokedCountSupportInternal = self::exactly($invokedCountSupport);
        $parser->expects($invokedCountSupportInternal)
            ->method('supports')
            ->willReturnCallback(static function (mixed ...$args) use ($invokedCountSupportInternal, $supportArgs, $supportReturnValue): mixed {
                self::assertSame($supportArgs[$invokedCountSupportInternal->numberOfInvocations()], $args);

                return $supportReturnValue[$invokedCountSupportInternal->numberOfInvocations()];
            })
        ;

        $invokedCountParseInternal = self::exactly($invokedCountParse);
        $parser->expects($invokedCountParseInternal)
            ->method('parse')
            ->willReturnCallback(static function (mixed ...$args) use ($invokedCountParseInternal, $parseArgs, $parseReturnValue, $strict): mixed {
                self::assertSame([$parseArgs[$invokedCountParseInternal->numberOfInvocations()], $strict], $args);

                return $parseReturnValue[$invokedCountParseInternal->numberOfInvocations()];
            })
        ;

        $sUT = new FileParser($parser);

        self::assertSame($expected, iterator_to_array($sUT->parse($content, $lineLength, $strict)));
    }
}

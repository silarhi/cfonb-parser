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

use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Silarhi\Cfonb\Banking\Noop;
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\FileParser;
use Silarhi\Cfonb\Tests\CfonbTestCase;

final class FileParserStreamTest extends CfonbTestCase
{
    /** @return resource */
    private function stream(string $content)
    {
        $stream = fopen('php://memory', 'r+');
        if (false === $stream) {
            throw new RuntimeException('Unable to open a memory stream');
        }
        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }

    /**
     * The lines a parser receives, and the error of the first line no parser supports, a line starting with X.
     *
     * @param positive-int $lineLength
     *
     * @return array{list<string>, ?string}
     */
    private function parseLines(string $content, int $lineLength, bool $stream): array
    {
        $parser = new class implements ParserInterface {
            /** @var list<string> */
            public array $lines = [];

            #[Override]
            public function parse(string $content, bool $strict): Noop
            {
                $this->lines[] = $content;

                return new Noop();
            }

            #[Override]
            public function supports(string $content): bool
            {
                return !str_starts_with($content, 'X');
            }
        };

        $sUT = new FileParser($parser);
        $error = null;
        try {
            foreach ($stream ? $sUT->parseStream($this->stream($content), $lineLength, true) : $sUT->parse($content, $lineLength, true) as $element) {
                self::assertInstanceOf(Noop::class, $element);
            }
        } catch (ParseException $e) {
            $error = $e->getMessage();
        }

        // The empty lines are parsed as Noop records: only the other ones matter
        return [array_values(array_filter($parser->lines, static fn (string $line): bool => '' !== $line)), $error];
    }

    /** @return iterable<string, array{string}> */
    public static function provideContentCase(): iterable
    {
        yield 'empty' => [''];
        yield 'only line breaks' => ["\n\r\n\r"];
        yield 'lf' => ["aaaa\nbbbb\ncccc"];
        yield 'crlf' => ["aaaa\r\nbbbb\r\ncccc\r\n"];
        yield 'cr' => ["aaaa\rbbbb\rcccc\r"];
        yield 'mixed' => ["aaaa\r\nbbbb\ncccc\rdddd"];
        yield 'empty lines around and between' => ["\n\naaaa\n\n\nbbbb\n\n"];
        yield 'no line break' => ['aaaabbbbcccc'];
        yield 'no line break, then line breaks' => ["\r\naaaabbbbcc\r\n\r\n"];
        yield 'long first line, then another line' => ["aaaabbbb\ncccc"];
        yield 'long first line, empty lines, another line' => ["aaaabbbb\n\n\ncccc"];
        yield 'error on line 3' => ["aaaa\n\nXbad\ncccc"];
        yield 'error after empty lines at the start' => ["\n\n\nXbad"];
        yield 'error in a file without line breaks' => ['aaaaXbadcccc'];
        yield 'error after a long first line' => ["aaaabbbb\n\nXbad"];
    }

    #[DataProvider('provideContentCase')]
    public function testSameAsParse(string $content): void
    {
        self::assertSame($this->parseLines($content, 4, false), $this->parseLines($content, 4, true));
    }

    public function testCrlfSplitBetweenTwoChunks(): void
    {
        // The chunks are 65536 bytes: the "\r" is the last byte of the first one, the "\n" the first of the second
        $first = str_repeat('a', 65535);
        $content = $first . "\r\nbbbb\rcccc";

        self::assertSame([[$first, 'bbbb', 'cccc'], null], $this->parseLines($content, 4, true));
        self::assertSame($this->parseLines($content, 4, false), $this->parseLines($content, 4, true));
    }

    public function testCrAtTheEndOfAChunk(): void
    {
        $first = str_repeat('a', 65535);
        $content = $first . "\rbbbb";

        self::assertSame([[$first, 'bbbb'], null], $this->parseLines($content, 4, true));
    }

    public function testCrAtTheEndOfTheFile(): void
    {
        self::assertSame([['aaaa'], null], $this->parseLines("aaaa\r", 4, true));
    }

    public function testStreamThatCannotBeRead(): void
    {
        $stream = fopen($this->temporaryFile('aaaa'), 'w');
        if (false === $stream) {
            throw new RuntimeException('Unable to open a temporary file');
        }

        // fread() on a write-only stream raises a notice ("errno=9 Bad file descriptor") and returns false
        set_error_handler(static fn (): bool => true, \E_NOTICE);
        try {
            (new FileParser())->parseStream($stream, 4, true)->current();
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertSame('Unable to read the CFONB file', $e->getMessage());
        } finally {
            restore_error_handler();
            fclose($stream);
        }
    }
}

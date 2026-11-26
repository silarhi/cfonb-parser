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

namespace Silarhi\Cfonb\Parser;

use Generator;
use Silarhi\Cfonb\Banking\Element;
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;
use function strlen;

/** @internal */
final readonly class FileParser
{
    /** The bytes read at once from a stream */
    private const CHUNK_SIZE = 65536;

    /** @var ParserInterface[] */
    private array $parsers;

    public function __construct(ParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @param positive-int $lineLength
     *
     * @return Generator<int, Element>
     */
    public function parse(string $content, int $lineLength, bool $strict): iterable
    {
        $content = rtrim(str_replace(["\r\n", "\r"], "\n", $content), "\n");
        // The empty lines at the start of the file are skipped, but counted by the line numbers of the errors.
        $lineNumber = strspn($content, "\n");
        $content = ltrim($content, "\n");

        if ('' === $content) {
            return [];
        }

        if (strlen($content) > $lineLength && !str_contains($content, "\n")) {
            $content = chunk_split($content, $lineLength, "\n");
        }

        // Iterate lines without materialising an array of every line (same semantics as explode("\n")).
        $offset = 0;
        while (false !== ($pos = strpos($content, "\n", $offset))) {
            $line = substr($content, $offset, $pos - $offset);
            $offset = $pos + 1;

            yield $this->findSupportedParserForLine($line, ++$lineNumber)->parse($line, $strict);
        }

        $line = substr($content, $offset);

        yield $this->findSupportedParserForLine($line, ++$lineNumber)->parse($line, $strict);
    }

    /**
     * Parses a stream line by line, with the results of {@see parse()}: the memory used does not grow with the size of
     * the file. A file without line breaks is read in full, as {@see parse()} does.
     *
     * @param resource     $stream
     * @param positive-int $lineLength
     *
     * @return Generator<int, Element>
     *
     * @throws ParseException when the stream cannot be read
     */
    public function parseStream($stream, int $lineLength, bool $strict): Generator
    {
        $lineNumber = 0;
        $started = false;
        // The empty lines after the last one that is not empty are skipped, as parse() does: they wait for the next one
        $pendingEmptyLines = 0;
        // A first line longer than a record is a file without line breaks when no other line follows it
        $longLine = null;
        $longLineNumber = 0;

        foreach ($this->readLines($stream) as $line) {
            ++$lineNumber;
            if ('' === $line) {
                // The empty lines at the start of the file are skipped, but counted by the line numbers of the errors.
                $pendingEmptyLines += $started ? 1 : 0;

                continue;
            }

            if (null !== $longLine) {
                yield $this->findSupportedParserForLine($longLine, $longLineNumber)->parse($longLine, $strict);
                $longLine = null;
            }

            for ($emptyLineNumber = $lineNumber - $pendingEmptyLines; $emptyLineNumber < $lineNumber; ++$emptyLineNumber) {
                yield $this->findSupportedParserForLine('', $emptyLineNumber)->parse('', $strict);
            }
            $pendingEmptyLines = 0;

            if (!$started && strlen($line) > $lineLength) {
                $started = true;
                $longLine = $line;
                $longLineNumber = $lineNumber;

                continue;
            }

            $started = true;
            yield $this->findSupportedParserForLine($line, $lineNumber)->parse($line, $strict);
        }

        if (null !== $longLine) {
            foreach (str_split($longLine, $lineLength) as $record) {
                yield $this->findSupportedParserForLine($record, $longLineNumber++)->parse($record, $strict);
            }
        }
    }

    /**
     * The lines of a stream, split on "\n", "\r\n" and "\r", without their line break.
     *
     * @param resource $stream
     *
     * @return Generator<int, string>
     */
    private function readLines($stream): Generator
    {
        $buffer = '';
        while (!feof($stream)) {
            $chunk = fread($stream, self::CHUNK_SIZE);
            if (false === $chunk) {
                throw new ParseException('Unable to read the CFONB file');
            }
            $buffer .= $chunk;
            $eof = feof($stream);

            $offset = 0;
            $length = strlen($buffer);
            while (($position = $offset + strcspn($buffer, "\r\n", $offset)) < $length) {
                if ("\r" === $buffer[$position] && $position + 1 === $length && !$eof) {
                    // The "\r" may be the start of a "\r\n" split between two chunks
                    break;
                }

                yield substr($buffer, $offset, $position - $offset);
                $offset = $position + ("\r" === $buffer[$position] && "\n" === ($buffer[$position + 1] ?? '') ? 2 : 1);
            }
            $buffer = substr($buffer, $offset);
        }

        if ('' !== $buffer) {
            yield $buffer;
        }
    }

    private function findSupportedParserForLine(string $line, int $lineNumber): ParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($line)) {
                return $parser;
            }
        }

        // The line holds account numbers and labels: the message only gives its record code and length.
        throw new ParseException(sprintf('Unable to find a parser for the line %d: record code "%s", %d characters', $lineNumber, substr($line, 0, 2), strlen($line)), rawContent: $line);
    }
}

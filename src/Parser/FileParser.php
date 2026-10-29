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

<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Services;

use Silarhi\Cfonb\Contracts\LineParserInterface;
use Silarhi\Cfonb\Exceptions\ParseException;

/**
 * @internal
 */
abstract class AbstractReader
{

    abstract public function parse(string $content): array;

    /**
     * Length on line in content.
     * @return int
     */
    abstract protected function getLineLength(): int;

    /**
     * Line parsers array.
     * @return array
     */
    abstract protected function getLineParsers(): array;

    /**
     * Split content by lines.
     *
     * @param string $content
     *
     * @return array
     */
    protected function getLines(string $content): array
    {
        if (!empty($content) && strlen($content) > $this->getLineLength() && strpos($content, "\n") === false) {
            $content = chunk_split($content, $this->getLineLength(), "\n");
        }
        $lines = explode("\n", $content);

        // Remove last empty line if exists.
        $lines = array_filter($lines, function ($line) {
            return '' !== $line;
        });

        return $lines;
    }

    /**
     * @param string $line
     *
     * @return LineParserInterface
     */
    protected function resolveLineParser(string $line): LineParserInterface
    {
        foreach ($this->getLineParsers() as $lineParser) {
            if ($lineParser->supports($line)) {
                return $lineParser;
            }
        }

        throw new ParseException(sprintf("Unable to find a parser for the line :\n\"%s\"", $line));
    }
}

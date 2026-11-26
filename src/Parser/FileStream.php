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
use RuntimeException;

use function sprintf;

/** @internal */
final class FileStream
{
    /**
     * @return resource
     *
     * @throws RuntimeException when the file cannot be opened
     */
    public static function open(string $path)
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException(sprintf('Unable to open the CFONB file "%s": it does not exist or cannot be read', $path));
        }

        $stream = fopen($path, 'r');
        if (false === $stream) {
            throw new RuntimeException(sprintf('Unable to open the CFONB file "%s"', $path));
        }

        return $stream;
    }

    /**
     * Yields the values of the generator, then closes the stream: at the end, on an exception, or when the generator
     * is destroyed before its end.
     *
     * @template T
     *
     * @param resource          $stream
     * @param Generator<int, T> $values
     *
     * @return Generator<int, T>
     */
    public static function closeAfter($stream, Generator $values): Generator
    {
        try {
            yield from $values;
        } finally {
            fclose($stream);
        }
    }
}

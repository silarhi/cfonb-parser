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

namespace Silarhi\Cfonb;

use Generator;
use Silarhi\Cfonb\Banking\Element;
use Silarhi\Cfonb\Banking\Header;
use Silarhi\Cfonb\Banking\Total;
use Silarhi\Cfonb\Banking\Transaction;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Parser\Cfonb240\Line31Parser;
use Silarhi\Cfonb\Parser\Cfonb240\Line34Parser;
use Silarhi\Cfonb\Parser\Cfonb240\Line39Parser;
use Silarhi\Cfonb\Parser\EmptyParser;
use Silarhi\Cfonb\Parser\FileParser;

use function sprintf;

final readonly class Cfonb240Reader
{
    public const LINE_LENGTH = 240;

    private FileParser $fileParser;

    public function __construct(?FileParser $fileParser = null)
    {
        $fileParser ??= new FileParser(
            new Line31Parser(),
            new Line34Parser(),
            new Line39Parser(),
            new EmptyParser()
        );

        $this->fileParser = $fileParser;
    }

    /**
     * Reads the transfers of a CFONB 240 file.
     *
     * @return list<Transfer>
     *
     * @throws ParseException on the first line that fails to parse, or record out of order
     */
    public function parse(string $content, bool $strict = true): array
    {
        return iterator_to_array($this->build($this->fileParser->parse($content, self::LINE_LENGTH, $strict)), false);
    }

    /**
     * @param iterable<Element> $elements
     *
     * @return Generator<int, Transfer>
     */
    private function build(iterable $elements): Generator
    {
        // The transfer opened by a "31" header, until its "39" total closes it.
        $header = null;
        /** @var list<Transaction> $transactions */
        $transactions = [];

        foreach ($elements as $result) {
            if ($result instanceof Header) {
                if (null !== $header) {
                    throw new ParseException(sprintf('Unable to start a transfer with the "31" header record of sequence number %s: the previous transfer has no "39" total record', $result->getSequenceNumber()));
                }
                $header = $result;
            } elseif ($result instanceof Total) {
                if (null === $header) {
                    throw new ParseException(sprintf('Unable to attach the total with sequence number %s: missing "31" header record before it', $result->getSequenceNumber()));
                }
                yield new Transfer($header, $transactions, $result);
                // The "39" record closes the transfer: any further record needs a new "31" header.
                $header = null;
                $transactions = [];
            } elseif ($result instanceof Transaction) {
                if (null === $header) {
                    throw new ParseException(sprintf('Unable to attach the transaction with sequence number %s: missing "31" header record before it', $result->getSequenceNumber()));
                }
                $transactions[] = $result;
            }
        }

        if (null !== $header) {
            throw new ParseException(sprintf('Unable to close the transfer of the "31" header record of sequence number %s: missing "39" total record at the end of the file', $header->getSequenceNumber()));
        }
    }
}

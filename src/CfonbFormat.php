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

use function in_array;

use Silarhi\Cfonb\Exceptions\ParseException;

use function sprintf;

/** The two formats of CFONB files that the readers read. */
enum CfonbFormat: int
{
    /** Account statements: "01" old balance, "04" operations, "05" details, "07" new balance */
    case Cfonb120 = 120;
    /** Transfers: "31" header, "34" transactions, "39" total */
    case Cfonb240 = 240;

    private const CFONB_120_RECORD_CODES = ['01', '04', '05', '07'];
    private const CFONB_240_RECORD_CODES = ['31', '34', '39'];

    /**
     * The format of a file, from the record code of its first line: the record codes of the two formats differ.
     *
     * @throws ParseException when the file is empty, or its first record code is of neither format
     */
    public static function fromContent(string $content): self
    {
        $line = self::firstLine($content);
        if (null === $line) {
            throw new ParseException('Unable to detect the CFONB format: the content is empty');
        }

        return self::fromRecordCode(substr($line, 0, 2))
            // The line holds account numbers and labels: the message only gives its record code.
            ?? throw new ParseException(sprintf('Unable to detect the CFONB format: the record code "%s" of the first line is neither a CFONB 120 record (01, 04, 05, 07) nor a CFONB 240 record (31, 34, 39)', substr($line, 0, 2)), rawContent: $line);
    }

    /** The format of a file, null when it is empty or its first record code is of neither format. */
    public static function tryFromContent(string $content): ?self
    {
        $line = self::firstLine($content);

        return null !== $line ? self::fromRecordCode(substr($line, 0, 2)) : null;
    }

    /** The length of the records, 120 or 240 characters */
    public function lineLength(): int
    {
        return $this->value;
    }

    private static function fromRecordCode(string $recordCode): ?self
    {
        return match (true) {
            in_array($recordCode, self::CFONB_120_RECORD_CODES, true) => self::Cfonb120,
            in_array($recordCode, self::CFONB_240_RECORD_CODES, true) => self::Cfonb240,
            default => null,
        };
    }

    /** The first line that is not empty, null when there is none */
    private static function firstLine(string $content): ?string
    {
        $content = ltrim($content, "\r\n");
        if ('' === $content) {
            return null;
        }

        return substr($content, 0, strcspn($content, "\r\n"));
    }
}

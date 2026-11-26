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

use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Exceptions\ParseException;

final readonly class CfonbReader
{
    public function __construct(
        private Cfonb120Reader $cfonb120Reader = new Cfonb120Reader(),
        private Cfonb240Reader $cfonb240Reader = new Cfonb240Reader(),
    ) {
    }

    /**
     * Reads a CFONB 120 or CFONB 240 file, detected by {@see CfonbFormat::fromContent()}: the statements of a CFONB
     * 120 file, the transfers of a CFONB 240 file. An empty file has neither.
     *
     * @return list<Statement>|list<Transfer>
     *
     * @throws ParseException when the format is of neither, or on the first line that fails to parse
     */
    public function parse(string $content, bool $strict = true): array
    {
        if ('' === trim($content, "\r\n")) {
            return [];
        }

        return match (CfonbFormat::fromContent($content)) {
            CfonbFormat::Cfonb120 => $this->cfonb120Reader->parse($content, $strict),
            CfonbFormat::Cfonb240 => $this->cfonb240Reader->parse($content, $strict),
        };
    }

    /** @return list<Statement> */
    public function parseCfonb120(string $content, bool $strict = true): array
    {
        return $this->cfonb120Reader->parse($content, $strict);
    }

    /** @return list<Transfer> */
    public function parseCfonb240(string $content, bool $strict = true): array
    {
        return $this->cfonb240Reader->parse($content, $strict);
    }
}

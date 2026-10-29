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

namespace Silarhi\Cfonb\Exceptions;

use RuntimeException;
use Throwable;

class ParseException extends RuntimeException
{
    /**
     * @param string|null $rawContent the line, or the field, that failed to parse: kept out of the message, as it holds
     *                                account numbers and labels
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private readonly ?string $rawContent = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * The raw line (or, for an amount, the raw field) that failed to parse, to debug a file; null when the error is not
     * about a single line. It holds account numbers and labels: do not log it or show it as is.
     */
    public function getRawContent(): ?string
    {
        return $this->rawContent;
    }
}

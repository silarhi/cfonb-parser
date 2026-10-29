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

/**
 * Thrown by {@see \Silarhi\Cfonb\Banking\Statement::validate()} and {@see \Silarhi\Cfonb\Banking\Transfer::validate()}
 * on a statement or a transfer that parses but is not consistent.
 */
final class ValidationException extends ParseException
{
    public function __construct(private readonly ValidationCheck $check, string $message)
    {
        parent::__construct($message);
    }

    public function getCheck(): ValidationCheck
    {
        return $this->check;
    }
}

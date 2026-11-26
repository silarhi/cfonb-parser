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

/** Whether an {@see OperationCode} debits or credits the account. */
enum Direction: string
{
    case Debit = 'D';
    case Credit = 'C';
    /** Mostly a debit, sometimes a credit */
    case DebitOrCredit = 'D/C';
    /** Mostly a credit, sometimes a debit */
    case CreditOrDebit = 'C/D';

    public function canDebit(): bool
    {
        return self::Credit !== $this;
    }

    public function canCredit(): bool
    {
        return self::Debit !== $this;
    }
}

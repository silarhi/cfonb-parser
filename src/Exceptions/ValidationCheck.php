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
 * The check of {@see \Silarhi\Cfonb\Banking\Statement::validate()} or {@see \Silarhi\Cfonb\Banking\Transfer::validate()}
 * that failed.
 */
enum ValidationCheck: string
{
    /** A record holds another bank code than the "01" old balance (CFONB 120) or the "31" header (CFONB 240). */
    case BankCode = 'bank_code';

    /** A record holds another desk (counter) code than the "01" old balance or the "31" header. */
    case DeskCode = 'desk_code';

    /** A record holds another account number than the "01" old balance or the "31" header. */
    case AccountNumber = 'account_number';

    /** A record holds another currency than the "01" old balance or the "31" header. */
    case Currency = 'currency';

    /** CFONB 120: the "07" new balance is not the "01" old balance plus the "04" operations. */
    case Balance = 'balance';

    /** CFONB 240: the "39" total is not the sum of the "34" transactions. */
    case Total = 'total';
}

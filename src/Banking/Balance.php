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

namespace Silarhi\Cfonb\Banking;

use DateTimeImmutable;

final class Balance extends Element
{
    private readonly string $exactAmount;

    /**
     * @param string|null $exactAmount the exact amount, a decimal string such as "16695.65"; when null, e.g. on a
     *                                 balance built by hand, the float amount with 2 decimals
     */
    public function __construct(
        private readonly string $bankCode,
        private readonly string $deskCode,
        private readonly string $currencyCode,
        private readonly string $accountNumber,
        private readonly DateTimeImmutable $date,
        private readonly float $amount,
        ?string $exactAmount = null,
    ) {
        $this->exactAmount = null === $exactAmount ? ExactAmount::fromFloat($amount) : ExactAmount::check($exactAmount);
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function getDeskCode(): string
    {
        return $this->deskCode;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * The amount as a float by default, rounded like any float: pass true for the exact amount as a decimal string,
     * "16695.65", to store in a DECIMAL column or add with bcmath. 7.0 returns the string only.
     *
     * @return ($asString is true ? string : float)
     */
    public function getAmount(bool $asString = false): float|string
    {
        return $asString ? $this->exactAmount : $this->amount;
    }
}

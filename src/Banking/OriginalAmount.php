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

/**
 * The original amount of a transfer in another currency, from its "05" detail record of qualifier MMO.
 */
final readonly class OriginalAmount
{
    /**
     * @param string      $amount       the unsigned exact amount, a decimal string such as "1250.00"
     * @param string|null $exchangeRate the exchange rate, a decimal string such as "1.0825", when the bank gives it
     */
    public function __construct(
        private string $currencyCode,
        private string $amount,
        private ?string $exchangeRate,
    ) {
        ExactAmount::check($amount);
        if (null !== $exchangeRate) {
            ExactAmount::check($exchangeRate);
        }
    }

    /** The ISO 4217 code of the original currency, "USD" */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /** The unsigned exact amount in the original currency, a decimal string such as "1250.00" */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /** The exchange rate, a decimal string such as "1.0825", or null when the bank does not give it */
    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }
}

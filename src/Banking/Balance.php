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
use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * @phpstan-type BalanceArray array{bank_code: string, desk_code: string, account_number: string, currency_code: string, date: string, amount: string}
 */
final class Balance extends Element implements JsonSerializable
{
    private readonly string $amount;

    /**
     * @param float|string $amount the exact amount, a decimal string such as "16695.65", or a float, kept as
     *                             the decimal string that gives it back
     *
     * @throws InvalidArgumentException when $amount is a string that is not a decimal, or INF or NAN
     */
    public function __construct(
        private readonly string $bankCode,
        private readonly string $deskCode,
        private readonly string $currencyCode,
        private readonly string $accountNumber,
        private readonly DateTimeImmutable $date,
        float|string $amount,
    ) {
        $this->amount = ExactAmount::from($amount);
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

    /** The account of the balance, with its RIB key and IBAN */
    public function getAccount(): Account
    {
        return new Account($this->bankCode, $this->deskCode, $this->accountNumber);
    }

    /**
     * The amount as a float by default, rounded like any float: pass true for the exact amount as a decimal string,
     * "16695.65", to store in a DECIMAL column or add with bcmath. 7.0 returns the string only.
     *
     * @return ($asString is true ? string : float)
     */
    public function getAmount(bool $asString = false): float|string
    {
        return $asString ? $this->amount : (float) $this->amount;
    }

    /**
     * The balance as an array of scalars, for JSON: the amount is the exact decimal string, the date is Y-m-d.
     *
     * @return BalanceArray
     */
    public function toArray(): array
    {
        return [
            'bank_code' => $this->bankCode,
            'desk_code' => $this->deskCode,
            'account_number' => $this->accountNumber,
            'currency_code' => $this->currencyCode,
            'date' => $this->date->format('Y-m-d'),
            'amount' => $this->amount,
        ];
    }

    /** @return BalanceArray */
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

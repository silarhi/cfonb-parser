<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Banking;

class Balance
{
    /** @var string */
    private $bankCode;

    /** @var string */
    private $deskCode;

    /** @var string */
    private $currencyCode;

    /** @var string */
    private $accountNumber;

    /** @var \DateTimeImmutable */
    private $date;

    /** @var float */
    private $amount;

    public function __construct(string $bankCode, string $deskCode, string $currencyCode, string $accountNumber, \DateTimeImmutable $date, float $amount)
    {
        $this->bankCode = $bankCode;
        $this->deskCode = $deskCode;
        $this->currencyCode = $currencyCode;
        $this->accountNumber = $accountNumber;
        $this->date = $date;
        $this->amount = $amount;
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

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}

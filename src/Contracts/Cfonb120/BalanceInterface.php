<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts\Cfonb120;

use DateTimeInterface;
use Silarhi\Cfonb\Contracts\ReadItemInterface;

/**
 * Interface StatementInterface specifies general methods for model,
 * those will not changed for the version.
 */
interface BalanceInterface extends ReadItemInterface
{
    /**
     * @return string
     */
    public function getBankCode(): string;

    /**
     * @return string
     */
    public function getDeskCode(): string;

    /**
     * @return string
     */
    public function getCurrencyCode(): string;

    /**
     * @return string
     */
    public function getAccountNumber(): string;

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface;

    /**
     * @return float
     */
    public function getAmount(): float;
}

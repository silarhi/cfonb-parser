<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts\Cfonb240;

use DateTimeInterface;
use Silarhi\Cfonb\Contracts\ReadItemInterface;

/**
 * Interface TotalInterface specifies general methods for model,
 * those will not changed for the version.
 */
interface TotalInterface extends ReadItemInterface
{
    /**
     * @return int
     */
    public function getSequenceNumber(): int;

    /**
     * @return string|null
     */
    public function getOperationCode(): ?string;

    /**
     * @return DateTimeInterface
     */
    public function getCreationTransactionFileDate(): DateTimeInterface;

    /**
     * @return string
     */
    public function getCurrencyIndex(): string;

    /**
     * @return string
     */
    public function getRecipientBankCode1(): string;

    /**
     * @return string
     */
    public function getRecipientCounterCode1(): string;

    /**
     * @return string
     */
    public function getRecipientAccountNumber1(): string;

    /**
     * @return string|null
     */
    public function getRecipientName1(): ?string;

    /**
     * @return string
     */
    public function getRecipientBankCode2(): string;

    /**
     * @return string
     */
    public function getRecipientCounterCode2(): string;

    /**
     * @return string
     */
    public function getRecipientAccountNumber2(): string;

    /**
     * @return string|null
     */
    public function getRecipientName2(): ?string;

    /**
     * @return string|null
     */
    public function getProcessingCenterCode(): ?string;

    /**
     * @return float
     */
    public function getTotalAmount(): float;
}

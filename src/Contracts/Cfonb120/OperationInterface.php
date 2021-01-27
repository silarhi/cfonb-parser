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
 * Interface OperationInterface specifies general methods for model,
 * those will not changed for the version.
 */
interface OperationInterface extends ReadItemInterface
{
    /**
     * @return string
     */
    public function getBankCode(): string;

    /**
     * @return string|null
     */
    public function getInternalCode(): ?string;

    /**
     * @return string
     */
    public function getDeskCode(): string;

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string;

    /**
     * @return string
     */
    public function getAccountNumber(): string;

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface;

    /**
     * @return string|null
     */
    public function getRejectCode(): ?string;

    /**
     * @return DateTimeInterface
     */
    public function getValueDate(): DateTimeInterface;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getReference(): string;

    /**
     * @return string|null
     */
    public function getExemptCode(): ?string;

    /**
     * @return float
     */
    public function getAmount(): float;

    public function addDetail(DetailInterface $detail): OperationInterface;

    /**
     * @return DetailInterface[]
     */
    public function getDetails(): array;
}

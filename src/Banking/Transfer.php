<?php

declare(strict_types=1);

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

class Transfer
{
    /**
     * @var Header
     */
    private $header;

    /**
     * @var Transaction[]
     */
    private $transactions;

    /**
     * @var Total
     */
    private $total;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function setHeader(Header $header): void
    {
        $this->header = $header;
    }

    public function getHeader(): Header
    {
        return $this->header;
    }

    public function addTransaction(Transaction $operation): void
    {
        $this->transactions[] = $operation;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function setTotal(Total $total): void
    {
        $this->total = $total;
    }

    public function getTotal(): Total
    {
        return $this->total;
    }
}

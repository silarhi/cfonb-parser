<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Models\Cfonb240;

use Silarhi\Cfonb\Contracts\Cfonb240\HeaderInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TransactionInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TotalInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TransferInterface;

class Transfer implements TransferInterface
{

    /**
     * @var HeaderInterface
     */
    private $header;

    /**
     * @var TransactionInterface[]
     */
    private $transactions;

    /**
     * @var TotalInterface
     */
    private $total;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function setHeader(HeaderInterface $header): void
    {
        $this->header = $header;
    }

    public function getHeader(): HeaderInterface
    {
        return $this->header;
    }

    public function addTransaction(TransactionInterface $operation): void
    {
        $this->transactions[] = $operation;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function setTotal(TotalInterface $total): void
    {
        $this->total = $total;
    }

    public function getTotal(): TotalInterface
    {
        return $this->total;
    }
}

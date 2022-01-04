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

use Silarhi\Cfonb\Exceptions\HeaderUnavailableException;
use Silarhi\Cfonb\Exceptions\TotalUnavailableException;

class Transfer
{
    /**
     * @var Header|null
     */
    private $header;

    /**
     * @var Transaction[]
     */
    private $transactions;

    /**
     * @var Total|null
     */
    private $total;

    public function __construct()
    {
        $this->header = null;
        $this->total = null;
        $this->transactions = [];
    }

    public function setHeader(Header $header): void
    {
        $this->header = $header;
    }

    public function getHeader(): Header
    {
        if (null === $this->header) {
            throw new HeaderUnavailableException();
        }

        return $this->header;
    }

    public function addTransaction(Transaction $operation): void
    {
        $this->transactions[] = $operation;
    }

    /** @return Transaction[] */
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
        if (null === $this->total) {
            throw new TotalUnavailableException();
        }

        return $this->total;
    }
}

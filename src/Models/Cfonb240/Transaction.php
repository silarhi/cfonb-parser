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
use Silarhi\Cfonb\Contracts\Cfonb240\OperationInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TotalInterface;
use Silarhi\Cfonb\Contracts\Cfonb240\TransactionInterface;

class Transaction implements TransactionInterface
{

    /**
     * @var HeaderInterface
     */
    private $header;

    /**
     * @var OperationInterface[]
     */
    private $operations;

    /**
     * @var TotalInterface
     */
    private $total;

    public function __construct()
    {
        $this->operations = [];
    }

    public function setHeader(HeaderInterface $header): void
    {
        $this->header = $header;
    }

    public function getHeader(): HeaderInterface
    {
        return $this->header;
    }

    public function addOperation(OperationInterface $operation): void
    {
        $this->operations[] = $operation;
    }

    public function getOperations(): array
    {
        return $this->operations;
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

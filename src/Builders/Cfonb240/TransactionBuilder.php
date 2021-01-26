<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Builders\Cfonb240;

use Silarhi\Cfonb\Models\Cfonb240\Header;
use Silarhi\Cfonb\Models\Cfonb240\Operation;
use Silarhi\Cfonb\Models\Cfonb240\Total;
use Silarhi\Cfonb\Models\Cfonb240\Transaction;

/**
 * Builder for instance of class @see Transaction
 */
class TransactionBuilder
{

    /**
     * @var Transaction
     */
    private $instance;

    public function createInstance(): TransactionBuilder
    {
        $this->instance = new Transaction();

        return $this;
    }

    public function putHeader(Header $header): TransactionBuilder
    {
        $this->instance->setHeader($header);

        return $this;
    }

    public function putTotal(Total $total): TransactionBuilder
    {
        $this->instance->setTotal($total);

        return $this;
    }

    public function addOperation(Operation $operation): TransactionBuilder
    {
        $this->instance->addOperation($operation);

        return $this;
    }

    public function popInstance(): Transaction
    {
        $instance = $this->instance;
        $this->instance = null;

        return $instance;
    }
}

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

use RuntimeException;
use Silarhi\Cfonb\Models\Cfonb240\Header;
use Silarhi\Cfonb\Models\Cfonb240\Transaction;
use Silarhi\Cfonb\Models\Cfonb240\Total;
use Silarhi\Cfonb\Models\Cfonb240\Transfer;

/**
 * Builder for instance of class @see Transfer
 */
class TransferBuilder
{

    /**
     * @var Transfer|null
     */
    private $instance;

    public function createInstance(): TransferBuilder
    {
        $this->instance = new Transfer();

        return $this;
    }

    public function putHeader(Header $header): TransferBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->setHeader($header);

        return $this;
    }

    public function putTotal(Total $total): TransferBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->setTotal($total);

        return $this;
    }

    public function addTransaction(Transaction $transaction): TransferBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->addTransaction($transaction);

        return $this;
    }

    public function popInstance(): Transfer
    {
        if (!($instance = $this->instance)) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance = null;

        return $instance;
    }
}

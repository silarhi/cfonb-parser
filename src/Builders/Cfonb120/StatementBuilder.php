<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Builders\Cfonb120;

use RuntimeException;
use Silarhi\Cfonb\Models\Cfonb120\Detail;
use Silarhi\Cfonb\Models\Cfonb120\NewBalance;
use Silarhi\Cfonb\Models\Cfonb120\OldBalance;
use Silarhi\Cfonb\Models\Cfonb120\Operation;
use Silarhi\Cfonb\Models\Cfonb120\Statement;

/**
 * Builder for instance of class @see Statement
 */
class StatementBuilder
{

    /**
     * @var Statement|null
     */
    private $instance;

    public function createInstance(): StatementBuilder
    {
        $this->instance = new Statement();

        return $this;
    }

    public function putOldBalance(OldBalance $oldBalance): StatementBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->setOldBalance($oldBalance);

        return $this;
    }

    public function putNewBalance(NewBalance $oldBalance): StatementBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->setNewBalance($oldBalance);

        return $this;
    }

    public function addOperation(Operation $operation): StatementBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->addOperation($operation);

        return $this;
    }

    public function lastOperationAddDetail(Detail $detail): StatementBuilder
    {
        if (null === $this->instance) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance->getLastOperation()->addDetail($detail);

        return $this;
    }

    public function popInstance(): Statement
    {
        if (!($instance = $this->instance)) {
            throw new RuntimeException('Instance not defined');
        }

        $this->instance = null;

        return $instance;
    }
}

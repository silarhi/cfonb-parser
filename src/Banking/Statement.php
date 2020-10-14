<?php

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

use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;

class Statement
{
    /** @var Balance|null */
    private $oldBalance;

    /** @var Balance|null */
    private $newBalance;

    /** @var Operation[] */
    private $operations;

    public function __construct()
    {
        $this->oldBalance = null;
        $this->newBalance = null;
        $this->operations = [];
    }

    public function addOperation(Operation $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function hasOldBalance(): bool
    {
        return $this->oldBalance !== null;
    }

    public function getOldBalance(): Balance
    {
        if ($this->oldBalance === null) {
            throw new BalanceUnavailableException('old balance is null');
        }

        return $this->oldBalance;
    }

    public function setOldBalance(Balance $oldBalance): self
    {
        $this->oldBalance = $oldBalance;

        return $this;
    }

    public function hasNewBalance(): bool
    {
        return $this->newBalance !== null;
    }

    public function getNewBalance(): Balance
    {
        if ($this->newBalance === null) {
            throw new BalanceUnavailableException('new balance is null');
        }

        return $this->newBalance;
    }

    public function setNewBalance(Balance $newBalance): self
    {
        $this->newBalance = $newBalance;

        return $this;
    }

    /**
     * @return Operation[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }
}

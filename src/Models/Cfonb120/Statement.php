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

namespace Silarhi\Cfonb\Models\Cfonb120;

use RuntimeException;
use Silarhi\Cfonb\Contracts\Cfonb120\BalanceInterface;
use Silarhi\Cfonb\Contracts\Cfonb120\OperationInterface;
use Silarhi\Cfonb\Contracts\Cfonb120\StatementInterface;
use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;

class Statement implements StatementInterface
{
    /** @var BalanceInterface|null */
    private $oldBalance;

    /** @var BalanceInterface|null */
    private $newBalance;

    /** @var OperationInterface[] */
    private $operations;

    public function __construct()
    {
        $this->operations = [];
    }

    public function addOperation(OperationInterface $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function hasOldBalance(): bool
    {
        return $this->oldBalance !== null;
    }

    public function getOldBalance(): BalanceInterface
    {
        if ($this->oldBalance === null) {
            throw new BalanceUnavailableException('old balance is null');
        }

        return $this->oldBalance;
    }

    public function setOldBalance(BalanceInterface $oldBalance): self
    {
        $this->oldBalance = $oldBalance;

        return $this;
    }

    public function hasNewBalance(): bool
    {
        return $this->newBalance !== null;
    }

    public function getNewBalance(): BalanceInterface
    {
        if ($this->newBalance === null) {
            throw new BalanceUnavailableException('new balance is null');
        }

        return $this->newBalance;
    }

    public function setNewBalance(BalanceInterface $newBalance): self
    {
        $this->newBalance = $newBalance;

        return $this;
    }

    public function getOperations(): array
    {
        return $this->operations;
    }

    public function getLastOperation(): OperationInterface
    {
        if (!($lastOperation = end($this->operations))) {
            throw new RuntimeException('Can not get last operation.');
        }
        return $lastOperation;
    }
}

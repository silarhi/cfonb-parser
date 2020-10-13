<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Banking;

class Statement
{
    /** @var Balance|null */
    private $oldBalance;

    /** @var Balance|null */
    private $newBalance;

    /** @var Operation[] */
    private $operations = [];

    public function __construct()
    {
        $this->oldBalance = null;
        $this->newBalance = null;
    }

    public function addOperation(Operation $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function getOldBalance(): ?Balance
    {
        return $this->oldBalance;
    }

    public function setOldBalance(Balance $oldBalance): self
    {
        $this->oldBalance = $oldBalance;

        return $this;
    }

    public function getNewBalance(): ?Balance
    {
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

    /**
     * @param Operation[] $operations
     */
    public function setOperations(array $operations): self
    {
        $this->operations = $operations;

        return $this;
    }
}

<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Andrew Svirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silarhi\Cfonb\Contracts\Cfonb240;

/**
 * Interface StatementInterface specifies general methods for model,
 * those will not changed for the version.
 */
interface TransactionInterface
{

    /**
     * @return HeaderInterface
     */
    public function getHeader(): HeaderInterface;

    /**
     * @return OperationInterface[]
     */
    public function getOperations(): array;

    /**
     * @return TotalInterface
     */
    public function getTotal(): TotalInterface;
}

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

use DateTimeImmutable;

class OperationDetail extends Element
{
    public function __construct(
        private readonly string $bankCode,
        private readonly string $deskCode,
        private readonly string $accountNumber,
        private readonly string $code,
        private readonly DateTimeImmutable $date,
        private readonly string $qualifier,
        private readonly ?string $additionalInformations,
        private readonly ?string $internalCode,
        private readonly ?string $currencyCode,
    ) {
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function getInternalCode(): ?string
    {
        return $this->internalCode;
    }

    public function getDeskCode(): string
    {
        return $this->deskCode;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getAdditionalInformations(): ?string
    {
        return $this->additionalInformations;
    }

    public function getQualifier(): string
    {
        return $this->qualifier;
    }
}

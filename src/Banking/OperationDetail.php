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
use JsonSerializable;
use Override;
use Silarhi\Cfonb\OperationCode;

/**
 * @phpstan-type OperationDetailArray array{bank_code: string, desk_code: string, account_number: string, code: string, date: string, qualifier: string, additional_information: ?string, internal_code: ?string, currency_code: ?string}
 */
final class OperationDetail extends Element implements JsonSerializable
{
    /** The additional information takes 70 characters, positions 49 to 118 of the line */
    private const ADDITIONAL_INFORMATION_LENGTH = 70;

    private readonly string $rawAdditionalInformation;

    /**
     * @param string|null $rawAdditionalInformation the 70 characters of the additional information, not trimmed, which
     *                                              holds two fields at fixed positions for some qualifiers. Defaults to
     *                                              $additionalInformations
     */
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
        ?string $rawAdditionalInformation = null,
    ) {
        $this->rawAdditionalInformation = str_pad($rawAdditionalInformation ?? $additionalInformations ?? '', self::ADDITIONAL_INFORMATION_LENGTH);
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

    /** The interbank operation code of the operation, null for a code the CFONB does not define */
    public function getOperationCode(): ?OperationCode
    {
        return OperationCode::tryFrom($this->code);
    }

    /**
     * @deprecated since 6.7, use {@see getAdditionalInformation()}. 7.0 removes it
     */
    public function getAdditionalInformations(): ?string
    {
        return $this->additionalInformations;
    }

    /** The additional information, trimmed, null when blank */
    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformations;
    }

    /**
     * A field of the additional information, trimmed, null when blank: a field of IPY, RCN, REF, RUM or MMO.
     *
     * @param int<0, 69> $offset the offset of the field in the additional information, 0 for position 49 of the line
     * @param int<1, 70> $length
     */
    public function getAdditionalInformationField(int $offset, int $length): ?string
    {
        $field = trim(substr($this->rawAdditionalInformation, $offset, $length));

        return '' !== $field ? $field : null;
    }

    /**
     * The 70 characters of the additional information, not trimmed.
     *
     * @internal
     */
    public function getRawAdditionalInformation(): string
    {
        return $this->rawAdditionalInformation;
    }

    public function getQualifier(): string
    {
        return $this->qualifier;
    }

    /** The qualifier, null for one the CFONB does not define */
    public function getDetailQualifier(): ?DetailQualifier
    {
        return DetailQualifier::tryFrom($this->qualifier);
    }

    /** @return OperationDetailArray */
    public function toArray(): array
    {
        return [
            'bank_code' => $this->bankCode,
            'desk_code' => $this->deskCode,
            'account_number' => $this->accountNumber,
            'code' => $this->code,
            'date' => $this->date->format('Y-m-d'),
            'qualifier' => $this->qualifier,
            'additional_information' => $this->additionalInformations,
            'internal_code' => $this->internalCode,
            'currency_code' => $this->currencyCode,
        ];
    }

    /** @return OperationDetailArray */
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

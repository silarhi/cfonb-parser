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

namespace Silarhi\Cfonb\Tests\Banking;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\DetailQualifier;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\OperationCode;

final class OperationDetailTest extends TestCase
{
    public function testGetter(): void
    {
        $date = new DateTimeImmutable();

        $sUT = new OperationDetail(
            'test',
            'test2',
            'test3',
            'test4',
            $date,
            'test5',
            'test6',
            'test7',
            'test8'
        );

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getAccountNumber());
        self::assertSame('test4', $sUT->getCode());
        self::assertSame($date, $sUT->getDate());
        self::assertSame('test5', $sUT->getQualifier());
        self::assertSame('test6', $sUT->getAdditionalInformation());
        self::assertSame('test7', $sUT->getInternalCode());
        self::assertSame('test8', $sUT->getCurrencyCode());
    }

    /** @psalm-suppress DeprecatedMethod the alias is deprecated since 6.7 */
    public function testDeprecatedAlias(): void
    {
        $sUT = new OperationDetail('10278', '02204', '00012345603', 'B1', new DateTimeImmutable(), 'LIB', 'DEDIBOX 3706114', null, 'EUR');

        self::assertSame('DEDIBOX 3706114', $sUT->getAdditionalInformations());
    }

    public function testCodes(): void
    {
        $sUT = new OperationDetail('10278', '02204', '00012345603', 'B1', new DateTimeImmutable(), 'NPY', 'ONLINE SAS', null, 'EUR');

        self::assertSame(OperationCode::SepaDirectDebitsDomiciled, $sUT->getOperationCode());
        self::assertSame(DetailQualifier::PayerName, $sUT->getDetailQualifier());

        $sUT = new OperationDetail('10278', '02204', '00012345603', 'ZZ', new DateTimeImmutable(), 'XYZ', null, null, null);

        self::assertNull($sUT->getOperationCode());
        self::assertNull($sUT->getDetailQualifier());
    }

    public function testAdditionalInformationField(): void
    {
        // Two fields of 35 characters: the second one starts at position 84 of the line
        $raw = str_pad('  E2E-REF-0001', 35) . str_pad('SALA', 35);
        $sUT = new OperationDetail('10278', '02204', '00012345603', '05', new DateTimeImmutable(), 'RCN', trim($raw), null, null, $raw);

        self::assertSame('E2E-REF-0001', $sUT->getAdditionalInformationField(0, 35));
        self::assertSame('SALA', $sUT->getAdditionalInformationField(35, 35));
        self::assertNull($sUT->getAdditionalInformationField(60, 10));
        self::assertSame($raw, $sUT->getRawAdditionalInformation());

        // Built by hand without the raw field: the trimmed one, padded to 70 characters
        $sUT = new OperationDetail('10278', '02204', '00012345603', '05', new DateTimeImmutable(), 'LIB', 'DEDIBOX', null, null);

        self::assertSame(str_pad('DEDIBOX', 70), $sUT->getRawAdditionalInformation());

        $sUT = new OperationDetail('10278', '02204', '00012345603', '05', new DateTimeImmutable(), 'LIB', null, null, null);

        self::assertNull($sUT->getAdditionalInformationField(0, 70));
    }

    public function testToArray(): void
    {
        $sUT = new OperationDetail('10278', '02204', '00012345603', 'B1', new DateTimeImmutable('2020-04-07'), 'LIB', 'DEDIBOX 3706114', null, 'EUR');

        self::assertSame([
            'bank_code' => '10278',
            'desk_code' => '02204',
            'account_number' => '00012345603',
            'code' => 'B1',
            'date' => '2020-04-07',
            'qualifier' => 'LIB',
            'additional_information' => 'DEDIBOX 3706114',
            'internal_code' => null,
            'currency_code' => 'EUR',
        ], $sUT->toArray());
        self::assertSame($sUT->toArray(), $sUT->jsonSerialize());
    }
}

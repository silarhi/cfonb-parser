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
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\OperationCode;

final class OperationTest extends TestCase
{
    public function testGetter(): void
    {
        $date = new DateTimeImmutable();
        $valueDate = new DateTimeImmutable();

        $sUT = new Operation(
            'test',
            'test2',
            'test3',
            'test4',
            $date,
            $valueDate,
            'test5',
            'test6',
            10.0,
            'test7',
            'test8',
            'test9',
            'test10',
            'test11',
        );

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getAccountNumber());
        self::assertSame('test4', $sUT->getCode());
        self::assertSame($date, $sUT->getDate());
        self::assertSame($valueDate, $sUT->getValueDate());
        self::assertSame('test5', $sUT->getLabel());
        self::assertSame('test6', $sUT->getReference());
        self::assertSame(10.0, $sUT->getAmount());
        // Built from a float: the decimal string that gives it back
        self::assertSame('10.00', $sUT->getAmount(true));
        self::assertSame('test7', $sUT->getInternalCode());
        self::assertSame('test8', $sUT->getCurrencyCode());
        self::assertSame('test9', $sUT->getRejectCode());
        self::assertSame('test10', $sUT->getExemptCode());
        self::assertSame('test11', $sUT->getBankOperationReference());
        self::assertCount(0, $sUT->getDetails());

        $detail = self::createStub(OperationDetail::class);

        $sUT->addDetails($detail);

        self::assertCount(1, $sUT->getDetails());
        self::assertSame([$detail], $sUT->getDetails());
    }

    public function testExactAmount(): void
    {
        $date = new DateTimeImmutable();

        $sUT = new Operation('test', 'test2', 'test3', 'test4', $date, $date, 'test5', 'test6', '-1669.565', null, null, null, null, null);

        self::assertSame(-1669.565, $sUT->getAmount());
        self::assertSame('-1669.565', $sUT->getAmount(true));

        // Built from a float: (int) (0.29 * 100) is 28, the decimal string is "0.29"
        $sUT = new Operation('test', 'test2', 'test3', 'test4', $date, $date, 'test5', 'test6', 0.29, null, null, null, null, null);

        self::assertSame('0.29', $sUT->getAmount(true));
        self::assertSame(0.29, $sUT->getAmount());

        // A float with 3 decimals keeps them
        $sUT = new Operation('test', 'test2', 'test3', 'test4', $date, $date, 'test5', 'test6', -1669.565, null, null, null, null, null);

        self::assertSame('-1669.565', $sUT->getAmount(true));
        self::assertSame(-1669.565, $sUT->getAmount());
    }

    private function operation(string $code = 'B1', OperationDetail ...$details): Operation
    {
        $date = new DateTimeImmutable('2020-04-07');

        $operation = new Operation('10278', '02204', '00012345603', $code, $date, $date, 'PRLV SEPA ONLINE SAS', 'REF', '-22.79', null, 'EUR', null, null, null);
        foreach ($details as $detail) {
            $operation->addDetails($detail);
        }

        return $operation;
    }

    private function detail(string $qualifier, ?string $information): OperationDetail
    {
        return new OperationDetail('10278', '02204', '00012345603', 'B1', new DateTimeImmutable('2020-04-07'), $qualifier, $information, null, 'EUR');
    }

    public function testOperationCode(): void
    {
        self::assertSame(OperationCode::SepaDirectDebitsDomiciled, $this->operation('B1')->getOperationCode());
        self::assertNull($this->operation('ZZ')->getOperationCode());
    }

    public function testAccount(): void
    {
        $account = $this->operation()->getAccount();

        self::assertSame('10278', $account->getBankCode());
        self::assertSame('02204', $account->getDeskCode());
        self::assertSame('00012345603', $account->getAccountNumber());
    }

    public function testDetailsByQualifier(): void
    {
        $label1 = $this->detail('LIB', 'DEDIBOX 3706114');
        $payer = $this->detail('NPY', 'ONLINE SAS');
        $label2 = $this->detail('LIB', 'FACTURE 2020-04');
        $sUT = $this->operation('B1', $label1, $payer, $label2);

        self::assertSame([$label1, $label2], $sUT->getDetailsByQualifier(DetailQualifier::Label));
        self::assertSame([$label1, $label2], $sUT->getDetailsByQualifier('LIB'));
        self::assertSame([], $sUT->getDetailsByQualifier(DetailQualifier::BeneficiaryName));
        self::assertSame($label1, $sUT->getDetail(DetailQualifier::Label));
        self::assertSame($payer, $sUT->getDetail('NPY'));
        self::assertNull($sUT->getDetail(DetailQualifier::MandateReference));
    }

    public function testFullLabel(): void
    {
        self::assertSame('PRLV SEPA ONLINE SAS', $this->operation()->getFullLabel());
        self::assertSame(
            'PRLV SEPA ONLINE SAS DEDIBOX 3706114 FACTURE 2020-04',
            $this->operation('B1', $this->detail('LIB', 'DEDIBOX 3706114'), $this->detail('NPY', 'ONLINE SAS'), $this->detail('LIB', null), $this->detail('LIB', 'FACTURE 2020-04'))->getFullLabel()
        );
    }

    public function testSepaInformation(): void
    {
        $sUT = $this->operation('B1', $this->detail('NPY', 'ONLINE SAS'), $this->detail('NBE', 'ACME'));

        self::assertSame('ONLINE SAS', $sUT->getSepaInformation()->getPayerName());
        self::assertSame('ACME', $sUT->getSepaInformation()->getBeneficiaryName());
        self::assertNull($sUT->getSepaInformation()->getMandateReference());
    }
}

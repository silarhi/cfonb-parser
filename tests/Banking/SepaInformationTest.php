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
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Banking\SepaInformation;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Tests\CfonbTestCase;

final class SepaInformationTest extends CfonbTestCase
{
    public function testDirectDebit(): void
    {
        $operation = (new Cfonb120Reader())->parse(self::loadFixture('sepa-test.txt', false))[0]->getOperations()[0];

        $sUT = $operation->getSepaInformation();

        self::assertSame('JOHN DOE', $sUT->getPayerName());
        self::assertSame('CUST-0042', $sUT->getPayerId());
        self::assertSame('CUST', $sUT->getPayerIdType());
        self::assertSame('ONLINE SAS', $sUT->getBeneficiaryName());
        self::assertSame('FR28ZZZ444555', $sUT->getBeneficiaryId());
        self::assertSame('SEPA', $sUT->getBeneficiaryIdType());
        // The word cut at the 70th character of the LCC line goes on in the LC2 line
        self::assertSame('FACTURE 2020-04-0042 ABONNEMENT DEDIBOX ET NOM DE DOMAINE EXEMPLE.FR PERIODE DU 01/04/2020 AU 30/04/2020', $sUT->getRemittanceInformation());
        self::assertSame('E2E-2020-04-0001', $sUT->getEndToEndReference());
        self::assertSame('OTHR', $sUT->getPurpose());
        self::assertSame('REMISE-42', $sUT->getRemittanceReference());
        self::assertSame('TX-0001', $sUT->getTransactionReference());
        self::assertSame('RUM-2019-000123', $sUT->getMandateReference());
        self::assertSame('RCUR', $sUT->getSequenceType());
        self::assertSame('FR7630006000011234567890189', $sUT->getDebtorAccount());
        self::assertNull($sUT->getUltimateDebtorName());
        self::assertNull($sUT->getOriginalAmount());
        self::assertNull($sUT->getBeneficiaryAccount());
        self::assertNull($sUT->getStructuredRemittanceInformation());
        self::assertSame('PRLV SEPA ONLINE SAS DEDIBOX 3706114', $operation->getFullLabel());
    }

    public function testTransfer(): void
    {
        $sUT = (new Cfonb120Reader())->parse(self::loadFixture('sepa-test.txt', false))[0]->getOperations()[1]->getSepaInformation();

        self::assertSame('ACME HOLDING', $sUT->getUltimateDebtorName());
        self::assertSame('ACMEHOLDXXX', $sUT->getUltimateDebtorId());
        self::assertSame('BICOrBEI', $sUT->getUltimateDebtorIdType());
        self::assertSame('DEDIBOX CUSTOMER', $sUT->getUltimateCreditorName());
        self::assertSame('DEDI-777', $sUT->getUltimateCreditorId());
        self::assertSame('CUST', $sUT->getUltimateCreditorIdType());
        self::assertSame('RF18539007547034', $sUT->getStructuredRemittanceInformation());
        self::assertSame('FR1420041010050500013M02606', $sUT->getBeneficiaryAccount());
        self::assertNull($sUT->getPayerName());
        self::assertNull($sUT->getRemittanceInformation());

        $originalAmount = $sUT->getOriginalAmount();
        self::assertNotNull($originalAmount);
        self::assertSame('USD', $originalAmount->getCurrencyCode());
        self::assertSame('1250.00', $originalAmount->getAmount());
        self::assertSame('1.0825', $originalAmount->getExchangeRate());
    }

    private function detail(string $qualifier, string $raw): OperationDetail
    {
        $information = trim($raw);

        return new OperationDetail('10278', '02204', '00012345603', '05', new DateTimeImmutable(), $qualifier, '' !== $information ? $information : null, null, null, str_pad($raw, 70));
    }

    public function testNoDetail(): void
    {
        $sUT = new SepaInformation();

        self::assertNull($sUT->getPayerName());
        self::assertNull($sUT->getRemittanceInformation());
        self::assertNull($sUT->getOriginalAmount());
    }

    public function testFirstDetailOfAQualifier(): void
    {
        $sUT = new SepaInformation($this->detail('NPY', 'JOHN DOE'), $this->detail('NPY', 'JANE DOE'));

        self::assertSame('JOHN DOE', $sUT->getPayerName());
    }

    public function testRemittanceInformationOfOneLine(): void
    {
        self::assertSame('FACTURE 42', (new SepaInformation($this->detail('LCC', 'FACTURE 42')))->getRemittanceInformation());
        self::assertSame('FACTURE 42', (new SepaInformation($this->detail('LC2', 'FACTURE 42')))->getRemittanceInformation());
        self::assertNull((new SepaInformation($this->detail('LCC', '')))->getRemittanceInformation());
    }

    public function testOriginalAmountWithoutExchangeRate(): void
    {
        $originalAmount = (new SepaInformation($this->detail('MMO', 'GBP000000000012345')))->getOriginalAmount();

        self::assertNotNull($originalAmount);
        self::assertSame('GBP', $originalAmount->getCurrencyCode());
        // 0 decimals
        self::assertSame('12345', $originalAmount->getAmount());
        self::assertNull($originalAmount->getExchangeRate());
    }

    public function testOriginalAmountNotFilledIn(): void
    {
        self::assertNull((new SepaInformation($this->detail('MMO', 'usd200000000125000')))->getOriginalAmount());
        self::assertNull((new SepaInformation($this->detail('MMO', 'USDX00000000125000')))->getOriginalAmount());
        self::assertNull((new SepaInformation($this->detail('MMO', 'USD2000000001250AB')))->getOriginalAmount());
        self::assertNull((new SepaInformation($this->detail('MMO', '')))->getOriginalAmount());
    }
}

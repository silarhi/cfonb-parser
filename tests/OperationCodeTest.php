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

namespace Silarhi\Cfonb\Tests;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\CfonbCodes;
use Silarhi\Cfonb\Direction;
use Silarhi\Cfonb\OperationCode;

final class OperationCodeTest extends TestCase
{
    public function testCode(): void
    {
        $sUT = OperationCode::TransfersReceived;

        self::assertSame($sUT, OperationCode::from('05'));
        self::assertSame('Virements reçus', $sUT->label());
        self::assertSame(Direction::Credit, $sUT->direction());
        self::assertSame('TRF', $sUT->swiftCode());
        self::assertSame('Transfer', $sUT->swiftLabel());
    }

    public function testRefundsOfSepaDirectDebits(): void
    {
        // A5 had the label of A6, the business-to-business (interentreprises) one
        self::assertSame('Prélèvements SEPA – reversements émis (par le créancier ou sa banque)', OperationCode::SepaDirectDebitRefundsIssued->label());
        self::assertSame('Prélèvements SEPA interentreprises – reversements émis (par le créancier ou sa banque)', OperationCode::SepaB2bDirectDebitRefundsIssued->label());
    }

    /** @psalm-suppress DeprecatedProperty, DeprecatedClass CfonbCodes is deprecated since 6.7 */
    public function testSameCodesAsCfonbCodes(): void
    {
        $codes = [];
        foreach (OperationCode::cases() as $code) {
            $codes[$code->value] = ['label' => $code->label(), 'way' => $code->direction()->value, 'swift_code' => $code->swiftCode(), 'swift_label' => $code->swiftLabel(), 'swift_way' => $code->direction()->value];
        }

        self::assertSame(CfonbCodes::$codes, $codes);
    }
}

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
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Account;
use Silarhi\Cfonb\Banking\Balance;

final class AccountTest extends TestCase
{
    /** @return iterable<string, array{string, string, string, string, string}> */
    public static function provideAccountCase(): iterable
    {
        // The examples of the RIB key and IBAN specifications
        yield 'letters in the account number' => ['20041', '01005', '0500013M026', '06', 'FR1420041010050500013M02606'];
        yield 'digits only' => ['30006', '00001', '12345678901', '89', 'FR7630006000011234567890189'];
        yield 'statement fixture' => ['10278', '02204', '00012345603', '07', 'FR7610278022040001234560307'];
    }

    #[DataProvider('provideAccountCase')]
    public function testRibKeyAndIban(string $bankCode, string $deskCode, string $accountNumber, string $ribKey, string $iban): void
    {
        $sUT = new Account($bankCode, $deskCode, $accountNumber);

        self::assertSame($bankCode, $sUT->getBankCode());
        self::assertSame($deskCode, $sUT->getDeskCode());
        self::assertSame($accountNumber, $sUT->getAccountNumber());
        self::assertSame($ribKey, $sUT->getRibKey());
        self::assertSame($bankCode . $deskCode . $accountNumber . $ribKey, $sUT->getBban());
        self::assertSame($iban, $sUT->getIban());
        self::assertSame($iban, (string) $sUT);
        self::assertSame(implode(' ', str_split($iban, 4)), $sUT->getFormattedIban());
        self::assertSame(['bank_code' => $bankCode, 'desk_code' => $deskCode, 'account_number' => $accountNumber, 'rib_key' => $ribKey, 'iban' => $iban], $sUT->toArray());
        self::assertSame($sUT->toArray(), $sUT->jsonSerialize());
    }

    public function testIbanOfAnotherCountry(): void
    {
        // Monaco shares the French BBAN
        self::assertSame('MC5810278022040001234560307', (new Account('10278', '02204', '00012345603'))->getIban('MC'));
    }

    public function testRibKeyOf97(): void
    {
        // A remainder of 0 is the key 97, never 00
        $sUT = new Account('00000', '00000', '00000000000');

        self::assertSame('97', $sUT->getRibKey());
    }

    public function testInvalidCountryCode(): void
    {
        try {
            (new Account('10278', '02204', '00012345603'))->getIban('fr');
            self::fail('An InvalidArgumentException should be thrown');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('The country code "fr" is not 2 capital letters.', $e->getMessage());
        }
    }

    /** @return iterable<string, array{string, string, string, string}> */
    public static function provideInvalidCase(): iterable
    {
        yield 'bank code too short' => ['1027', '02204', '00012345603', 'The bank code of an account must be 5 digits, not 4 characters.'];
        yield 'bank code with a letter' => ['1027A', '02204', '00012345603', 'The bank code of an account must be 5 digits, not 5 characters.'];
        yield 'desk code too long' => ['10278', '022040', '00012345603', 'The desk code of an account must be 5 digits, not 6 characters.'];
        yield 'account number too short' => ['10278', '02204', '0001234560', 'The account number of an account must be 11 digits or capital letters.'];
        yield 'account number in lower case' => ['10278', '02204', '0500013m026', 'The account number of an account must be 11 digits or capital letters.'];
    }

    #[DataProvider('provideInvalidCase')]
    public function testInvalid(string $bankCode, string $deskCode, string $accountNumber, string $message): void
    {
        try {
            new Account($bankCode, $deskCode, $accountNumber);
            self::fail('An InvalidArgumentException should be thrown');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString($message, $e->getMessage());
        }
    }

    public function testBalanceAccount(): void
    {
        $account = (new Balance('10278', '02204', 'EUR', '00012345603', new DateTimeImmutable(), '0.00'))->getAccount();

        self::assertSame('FR7610278022040001234560307', $account->getIban());
    }
}

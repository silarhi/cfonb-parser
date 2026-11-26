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

use InvalidArgumentException;

use function ord;

use Override;

use function sprintf;

use Stringable;

use function strlen;

/**
 * A French bank account, as a CFONB file gives it: the bank code, the desk code and the account number. Its RIB key
 * and its IBAN are computed from them.
 */
final readonly class Account implements Stringable
{
    /** The letters of an account number become digits to compute the RIB key: A and J are 1, B, K and S are 2... */
    private const RIB_LETTERS = [
        'A' => '1', 'B' => '2', 'C' => '3', 'D' => '4', 'E' => '5', 'F' => '6', 'G' => '7', 'H' => '8', 'I' => '9',
        'J' => '1', 'K' => '2', 'L' => '3', 'M' => '4', 'N' => '5', 'O' => '6', 'P' => '7', 'Q' => '8', 'R' => '9',
        'S' => '2', 'T' => '3', 'U' => '4', 'V' => '5', 'W' => '6', 'X' => '7', 'Y' => '8', 'Z' => '9',
    ];

    /**
     * @throws InvalidArgumentException when the bank code or the desk code is not 5 digits, or the account number not
     *                                  11 digits or capital letters. The message leaves the values out.
     */
    public function __construct(
        private string $bankCode,
        private string $deskCode,
        private string $accountNumber,
    ) {
        if (1 !== preg_match('/^\d{5}$/', $bankCode)) {
            throw new InvalidArgumentException(sprintf('The bank code of an account must be 5 digits, not %d characters.', strlen($bankCode)));
        }
        if (1 !== preg_match('/^\d{5}$/', $deskCode)) {
            throw new InvalidArgumentException(sprintf('The desk code of an account must be 5 digits, not %d characters.', strlen($deskCode)));
        }
        if (1 !== preg_match('/^[0-9A-Z]{11}$/', $accountNumber)) {
            throw new InvalidArgumentException('The account number of an account must be 11 digits or capital letters.');
        }
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function getDeskCode(): string
    {
        return $this->deskCode;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /** The 2 digits of the RIB key: "06" for 20041 01005 0500013M026 */
    public function getRibKey(): string
    {
        $accountNumber = strtr($this->accountNumber, self::RIB_LETTERS);
        $remainder = (89 * (int) $this->bankCode + 15 * (int) $this->deskCode + 3 * (int) $accountNumber) % 97;

        return str_pad((string) (97 - $remainder), 2, '0', \STR_PAD_LEFT);
    }

    /** The 23 characters of the French BBAN: bank code, desk code, account number and RIB key */
    public function getBban(): string
    {
        return $this->bankCode . $this->deskCode . $this->accountNumber . $this->getRibKey();
    }

    /**
     * The IBAN, without spaces: "FR1420041010050500013M02606". The BBAN is the French one, which Monaco ("MC") shares:
     * an account of another country does not fit in a CFONB record.
     *
     * @param string $countryCode the ISO 3166 code of the country of the account, 2 capital letters
     *
     * @throws InvalidArgumentException when the country code is not 2 capital letters
     */
    public function getIban(string $countryCode = 'FR'): string
    {
        if (1 !== preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException(sprintf('The country code "%s" is not 2 capital letters.', $countryCode));
        }

        $bban = $this->getBban();
        // ISO 13616: the BBAN, then the country code and "00", letters as numbers (A is 10), modulo 97
        $digits = preg_replace_callback('/[A-Z]/', static fn (array $letter): string => (string) (ord($letter[0]) - 55), $bban . $countryCode . '00');
        $remainder = 0;
        foreach (str_split((string) $digits, 7) as $chunk) {
            $remainder = (int) ($remainder . $chunk) % 97;
        }

        return $countryCode . str_pad((string) (98 - $remainder), 2, '0', \STR_PAD_LEFT) . $bban;
    }

    /** The IBAN in groups of 4 characters, as printed: "FR14 2004 1010 0505 0001 3M02 606" */
    public function getFormattedIban(string $countryCode = 'FR'): string
    {
        return implode(' ', str_split($this->getIban($countryCode), 4));
    }

    /** The IBAN */
    #[Override]
    public function __toString(): string
    {
        return $this->getIban();
    }
}

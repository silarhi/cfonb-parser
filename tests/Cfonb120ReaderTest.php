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

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Exceptions\ValidationCheck;
use Silarhi\Cfonb\Exceptions\ValidationException;

use function sprintf;

final class Cfonb120ReaderTest extends CfonbTestCase
{
    private const OLD_BALANCE = '0110278    02204EUR2 00012345603  060420                                                  0000000166956E060420070420    ';
    private const OPERATION = '0410556677202204EUR2 00012345603B1070420  070420PRLV SEPA ONLINE SAS             0000000000000000000227R                ';
    private const DETAIL = '0510556677202204EUR2 00012345603B1070420     LIBDEDIBOX 3706114                                                         ';
    private const NEW_BALANCE = '0710278    02204EUR2 00012345603  070420                                                  0000000166728F                ';

    public function testEmpty(): void
    {
        $reader = new Cfonb120Reader();

        self::assertCount(0, $reader->parse(''));
    }

    public function testFailUnknowLine(): void
    {
        $reader = new Cfonb120Reader();

        try {
            $reader->parse('abc ');
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertSame('Unable to find a parser for the line 1: record code "ab", 4 characters', $e->getMessage());
            self::assertSame('abc ', $e->getRawContent());
        }
    }

    public function testFailUnknowLineKeepsTheLineOutOfTheMessage(): void
    {
        $reader = new Cfonb120Reader();
        // The empty lines at the start of the file count in the line number
        $line = substr(self::OPERATION, 0, 119);

        try {
            $reader->parse(implode("\n", ['', self::OLD_BALANCE, $line, self::NEW_BALANCE]));
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertSame('Unable to find a parser for the line 3: record code "04", 119 characters', $e->getMessage());
            self::assertStringNotContainsString('00012345603', $e->getMessage());
            self::assertSame($line, $e->getRawContent());
        }
    }

    public function testFailCauseNoOperation(): void
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach a detail for operation with code B1');
        $reader->parse('0510556677202204EUR2 00012345603B1070420     LIBDEDIBOX 3706114                                                         ');
    }

    public function testFailCauseOutOfRangeDate(): void
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "320420"');
        $reader->parse('0110278    02204EUR2 00012345603  320420                                                  0000000166956E060420070420    ');
    }

    public function testFailCauseZeroFilledValueDate(): void
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse date "000000": the date is zero-filled (empty), but this field is mandatory');
        $reader->parse(implode("\n", [self::OLD_BALANCE, substr_replace(self::OPERATION, '000000', 42, 6), self::NEW_BALANCE]));
    }

    /** @return iterable<string, array{list<string>, string}> */
    public static function provideOutOfSequenceCase(): iterable
    {
        yield 'new balance first' => [
            [self::NEW_BALANCE, self::OLD_BALANCE, self::OPERATION, self::NEW_BALANCE],
            'Unable to close a statement with the "07" new balance of account 00012345603 dated 2020-04-07: missing "01" old balance record before it',
        ];
        yield 'operation before any old balance' => [
            [self::OPERATION, self::OLD_BALANCE, self::NEW_BALANCE],
            'Unable to attach the operation with code B1 dated 2020-04-07: missing "01" old balance record before it',
        ];
        yield 'operation after a new balance' => [
            [self::OLD_BALANCE, self::NEW_BALANCE, self::OPERATION],
            'Unable to attach the operation with code B1 dated 2020-04-07: missing "01" old balance record before it',
        ];
        yield 'detail after a new balance' => [
            [self::OLD_BALANCE, self::OPERATION, self::NEW_BALANCE, self::DETAIL],
            'Unable to attach a detail for operation with code B1',
        ];
        yield 'old balance while a statement is open' => [
            [self::OLD_BALANCE, self::OPERATION, self::OLD_BALANCE, self::NEW_BALANCE],
            'Unable to open a statement with the "01" old balance of account 00012345603 dated 2020-04-06: the statement of account 00012345603 opened on 2020-04-06 has no "07" new balance record',
        ];
        yield 'statement never closed' => [
            [self::OLD_BALANCE, self::NEW_BALANCE, self::OLD_BALANCE, self::OPERATION, self::DETAIL],
            'Unable to close the statement of account 00012345603 opened on 2020-04-06: missing "07" new balance record at the end of the file',
        ];
    }

    /** @param list<string> $lines */
    #[DataProvider('provideOutOfSequenceCase')]
    public function testFailOutOfSequence(array $lines, string $message): void
    {
        $reader = new Cfonb120Reader();

        // The sequence of the records is checked in strict and non-strict mode alike.
        foreach ([true, false] as $strict) {
            try {
                $reader->parse(implode("\n", $lines), $strict);
                self::fail(sprintf('A ParseException should be thrown in %s mode', $strict ? 'strict' : 'non-strict'));
            } catch (ParseException $e) {
                self::assertSame($message, $e->getMessage());
            }
        }
    }

    public function testCrLineEndings(): void
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse(str_replace("\n", "\r", self::loadFixture('complex-test.txt', false)));

        self::assertEquals($reader->parse(self::loadFixture('complex-test.txt', false)), $statements);
    }

    /** @return array<string, array<int, string>> */
    public static function provideMalformedLine(): array
    {
        return [
            'dateMalformed' => ['0110278    02204EUR2 00012345603  YYYYYY                                                  0000000166956E060420070420    ', 'Regex does not match the line: the field "date" at position 35 is invalid'],
            'amountMalformed' => ['0110278    02204EUR2 00012345603  101020                                                  0000000166956Y060420070420    ', 'Regex does not match the line: the field "amount" at position 91 is invalid'],
            'operationAmountMalformed' => [substr_replace(self::OPERATION, 'Y', 103, 1), 'Regex does not match the line: the field "amount" at position 91 is invalid'],
            'accountMalformed' => ['0110278    02204EUR2 000123456-3  060420                                                  0000000166956E060420070420    ', 'Regex does not match the line: the field "account_nb" at position 22 is invalid'],
        ];
    }

    #[DataProvider('provideMalformedLine')]
    public function testMalformedLine(string $line, string $message): void
    {
        $reader = new Cfonb120Reader();

        try {
            $reader->parse($line);
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            // The message names the field, the line stays out of it: it holds the account number and the label.
            self::assertSame($message, $e->getMessage());
            self::assertSame($line, $e->getRawContent());
        }
    }

    #[DataProvider('provideOneLineOrNot')]
    public function testUnusedParts(bool $oneLine): void
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse(self::loadFixture('test-unused-parts.txt', $oneLine));

        self::assertCount(8, $statements);
    }

    #[DataProvider('provideOneLineOrNot')]
    public function testSimpleTest(bool $oneLine): void
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse(self::loadFixture('simple-test.txt', $oneLine));

        self::assertCount(1, $statements);

        $statement = $statements[0];

        self::assertTrue($statement->hasOldBalance());

        self::assertSame('2020-04-03 00:00:00', $statement->getOldBalance()->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('EUR', $statement->getOldBalance()->getCurrencyCode());
        self::assertSame(16695.65, $statement->getOldBalance()->getAmount());
        self::assertSame('10278', $statement->getOldBalance()->getBankCode());
        self::assertSame('02204', $statement->getOldBalance()->getDeskCode());
        self::assertSame('00012345603', $statement->getOldBalance()->getAccountNumber());

        // Data are the same expect date
        self::assertTrue($statement->hasNewBalance());

        self::assertSame('2020-04-06 00:00:00', $statement->getNewBalance()->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('EUR', $statement->getNewBalance()->getCurrencyCode());
        self::assertSame(16695.65, $statement->getNewBalance()->getAmount());
        self::assertSame('10278', $statement->getNewBalance()->getBankCode());
        self::assertSame('02204', $statement->getNewBalance()->getDeskCode());
        self::assertSame('00012345603', $statement->getNewBalance()->getAccountNumber());
    }

    /** @return Generator<int, array<int, bool>> */
    public static function provideOneLineOrNot(): iterable
    {
        yield [true];
        yield [false];
    }

    #[DataProvider('provideOneLineOrNot')]
    public function testComplexTest(bool $oneLine): void
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse(self::loadFixture('complex-test.txt', $oneLine));

        self::assertCount(8, $statements);

        // Test first statement
        $statement = $statements[0];

        self::assertSame(16695.65, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-07 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-22.79, $operation->getAmount());
        self::assertSame('PRLV SEPA ONLINE SAS', $operation->getLabel());
        self::assertCount(1, $operation->getDetails());
        self::assertSame('DEDIBOX 3706114', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(16672.86, $statement->getNewBalance()->getAmount());

        // Test second statement
        $statement = $statements[1];

        self::assertSame(16672.86, $statement->getOldBalance()->getAmount());
        self::assertCount(3, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-20.11, $operation->getAmount());
        self::assertSame('PRLV SEPA FREE MOBILE', $operation->getLabel());
        self::assertCount(0, $operation->getDetails());

        $operation = $operations[1];

        self::assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-5000.0, $operation->getAmount());
        self::assertSame('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        self::assertCount(0, $operation->getDetails());

        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        $operation = $operations[2];

        self::assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-20.11, $operation->getAmount());
        self::assertSame('VIR BOR TEST / FOOBAR', $operation->getLabel());
        self::assertCount(0, $operation->getDetails());

        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        // Third statement
        $statement = $statements[2];

        self::assertSame(11652.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        // Test fourth statement
        $statement = $statements[3];

        self::assertSame(11652.75, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-10 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('2020-04-01 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        self::assertSame(-117.75, $operation->getAmount());
        self::assertSame('FACTURE SGT20022040001692', $operation->getLabel());
        self::assertCount(1, $operation->getDetails());
        self::assertSame('DONT TVA 11 39EUR', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(11535.0, $statement->getNewBalance()->getAmount());

        // 5th statement
        $statement = $statements[4];

        self::assertSame(11535.0, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11535.0, $statement->getNewBalance()->getAmount());

        // 6th statement
        $statement = $statements[5];

        self::assertSame(11535.0, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        self::assertSame('2020-04-14 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('2020-04-14 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        self::assertSame(-50.25, $operation->getAmount());
        self::assertSame('PRLV SEPA OVH SAS', $operation->getLabel());
        self::assertCount(2, $operation->getDetails());
        self::assertSame('PAYMENT ORDER 124359169', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(11484.75, $statement->getNewBalance()->getAmount());

        // 7th statement
        $statement = $statements[6];

        self::assertSame(11484.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11484.75, $statement->getNewBalance()->getAmount());

        // 8th statement
        $statement = $statements[7];

        self::assertSame(584353.02, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(584353.02, $statement->getNewBalance()->getAmount());
    }

    public function testExactAmounts(): void
    {
        $statement = (new Cfonb120Reader())->parse(implode("\n", [self::OLD_BALANCE, self::OPERATION, self::NEW_BALANCE]))[0];

        self::assertSame('16695.65', $statement->getOldBalance()->getAmount(false));
        self::assertSame('-22.79', $statement->getOperations()[0]->getAmount(false));
        self::assertSame('16672.86', $statement->getNewBalance()->getAmount(false));

        // 0.29: (int) ($operation->getAmount() * 100) is 28
        $operation = substr_replace(self::OPERATION, '0000000000002I', 90, 14);
        $newBalance = substr_replace(self::NEW_BALANCE, '0000000166959D', 90, 14);
        $statement = (new Cfonb120Reader())->parse(implode("\n", [self::OLD_BALANCE, $operation, $newBalance]))[0];

        self::assertSame(0.29, $statement->getOperations()[0]->getAmount());
        self::assertSame('0.29', $statement->getOperations()[0]->getAmount(false));
        $statement->validate();

        // 0 decimals (position 20)
        $statement = (new Cfonb120Reader())->parse(implode("\n", [substr_replace(self::OLD_BALANCE, '0', 19, 1), substr_replace(self::NEW_BALANCE, '0', 19, 1)]))[0];

        self::assertSame('1669565', $statement->getOldBalance()->getAmount(false));
    }

    /** @return iterable<string, array{string, bool}> */
    public static function provideConsistentFixture(): iterable
    {
        yield 'simple' => ['simple-test.txt', true];
        yield 'unused parts' => ['test-unused-parts.txt', true];
        yield 'non strict' => ['non-strict-test.txt', false];
    }

    #[DataProvider('provideConsistentFixture')]
    public function testValidateFixture(string $file, bool $strict): void
    {
        $statements = (new Cfonb120Reader())->parse(self::loadFixture($file, false), $strict);

        self::assertNotEmpty($statements);
        foreach ($statements as $statement) {
            $statement->validate();
        }
    }

    public function testValidateInconsistentFixture(): void
    {
        $statements = (new Cfonb120Reader())->parse(self::loadFixture('complex-test.txt', false));

        // The second statement has an operation the new balance does not account for ("VIR BOR TEST / FOOBAR")
        foreach ($statements as $i => $statement) {
            if (1 !== $i) {
                $statement->validate();
            }
        }

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid statement opened on 2020-04-07: the "01" old balance (16672.86) plus the sum of the operations (-5040.22) is 11632.64, not the "07" new balance (11652.75)');
        $statements[1]->validate();
    }

    public function testValidateOperationOfAnotherAccount(): void
    {
        // An operation of another account parses, but does not validate
        $operation = substr_replace(self::OPERATION, '99999999999', 21, 11);
        $statement = (new Cfonb120Reader())->parse(implode("\n", [self::OLD_BALANCE, $operation, self::NEW_BALANCE]))[0];

        self::assertSame('99999999999', $statement->getOperations()[0]->getAccountNumber());

        try {
            $statement->validate();
            self::fail('A ValidationException should be thrown');
        } catch (ValidationException $e) {
            self::assertSame(ValidationCheck::AccountNumber, $e->getCheck());
            self::assertSame('Invalid statement opened on 2020-04-06: the account number of the operation 1 (code B1, dated 2020-04-07) is not the one of the "01" old balance', $e->getMessage());
        }
    }

    #[DataProvider('provideOneLineOrNot')]
    public function testtrictModeTest(bool $oneLine): void
    {
        self::expectException(ParseException::class);
        $reader = new Cfonb120Reader();
        $reader->parse(self::loadFixture('non-strict-test.txt', $oneLine));
    }

    #[DataProvider('provideOneLineOrNot')]
    public function testNonStrictModeTest(bool $oneLine): void
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse(self::loadFixture('non-strict-test.txt', $oneLine), false);

        self::assertCount(8, $statements);

        // Test first statement
        $statement = $statements[0];

        self::assertSame(16695.65, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-07 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-22.79, $operation->getAmount());
        self::assertSame('PRLV SEPA ONLINE SAS', $operation->getLabel());
        self::assertCount(1, $operation->getDetails());
        self::assertSame('DEDIBOX 3706114', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(16672.86, $statement->getNewBalance()->getAmount());

        // Test second statement
        $statement = $statements[1];

        self::assertSame(16672.86, $statement->getOldBalance()->getAmount());
        self::assertCount(2, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-20.11, $operation->getAmount());
        self::assertSame('PRLV SEPA FREE MOBILE', $operation->getLabel());
        self::assertSame('', $operation->getReference());
        self::assertCount(0, $operation->getDetails());

        $operation = $operations[1];

        self::assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame(-5000.0, $operation->getAmount());
        self::assertSame('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        self::assertSame('', $operation->getReference());
        self::assertCount(0, $operation->getDetails());

        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        // Third statement
        $statement = $statements[2];

        self::assertSame(11652.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        // Test fourth statement
        $statement = $statements[3];

        self::assertSame(11652.75, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        self::assertSame('2020-04-10 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('2020-04-01 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        self::assertSame(-117.75, $operation->getAmount());
        self::assertSame('FACTURE SGT20022040001692', $operation->getLabel());
        self::assertCount(1, $operation->getDetails());
        self::assertSame('DONT TVA 11 39EUR', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(11535.0, $statement->getNewBalance()->getAmount());

        // 5th statement
        $statement = $statements[4];

        self::assertSame(11535.0, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11535.0, $statement->getNewBalance()->getAmount());

        // 6th statement
        $statement = $statements[5];

        self::assertSame(11535.0, $statement->getOldBalance()->getAmount());
        self::assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        self::assertSame('2020-04-14 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('2020-04-14 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        self::assertSame(-50.25, $operation->getAmount());
        self::assertSame('PRLV SEPA OVH SAS', $operation->getLabel());
        self::assertCount(2, $operation->getDetails());
        self::assertSame('PAYMENT ORDER 124359169', $operation->getDetails()[0]->getAdditionalInformations());

        self::assertSame(11484.75, $statement->getNewBalance()->getAmount());

        // 7th statement
        $statement = $statements[6];

        self::assertSame(11484.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11484.75, $statement->getNewBalance()->getAmount());

        // 8th statement
        $statement = $statements[7];

        self::assertSame(584353.02, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(584353.02, $statement->getNewBalance()->getAmount());
    }
}

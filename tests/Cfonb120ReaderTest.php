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

namespace Siarhi\Cfonb\Tests;

use Generator;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;
use Silarhi\Cfonb\Tests\CfonbTestE2e;

class Cfonb120ReaderTest extends CfonbTestE2e
{
    /** @return void */
    public function testEmpty()
    {
        $reader = new Cfonb120Reader();

        self::assertCount(0, $reader->parse(''));
    }

    /** @return void */
    public function testFailUnknowLine()
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        $reader->parse('abc ');
    }

    /** @return void */
    public function testFailCauseNoOperation()
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to attach a detail for operation with code B1');
        $reader->parse('0510556677202204EUR2 00012345603B1070420     LIBDEDIBOX 3706114                                                         ');
    }

    /** @return array<string, array<int, string>> */
    public function provideMalformedLine(): array
    {
        return [
            'dateMalformed' => ['0110278    02204EUR2 00012345603  YYYYYY                                                  0000000166956E060420070420    '],
            'amountMalformed' => ['0110278    02204EUR2 00012345603  101020                                                  0000000166956Y060420070420    '],
        ];
    }

    /**
     * @dataProvider provideMalformedLine
     *
     * @return void
     */
    public function testMalformedLine(string $line)
    {
        $reader = new Cfonb120Reader();

        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Regex does not match the line');
        $reader->parse($line);
    }

    /**
     * @dataProvider provideOneLineOrNot
     *
     * @return void
     */
    public function testSimpleTest(bool $oneLine)
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

        //Data are the same expect date
        self::assertTrue($statement->hasNewBalance());

        self::assertSame('2020-04-06 00:00:00', $statement->getNewBalance()->getDate()->format('Y-m-d H:i:s'));
        self::assertSame('EUR', $statement->getNewBalance()->getCurrencyCode());
        self::assertSame(16695.65, $statement->getNewBalance()->getAmount());
        self::assertSame('10278', $statement->getNewBalance()->getBankCode());
        self::assertSame('02204', $statement->getNewBalance()->getDeskCode());
        self::assertSame('00012345603', $statement->getNewBalance()->getAccountNumber());
    }

    /** @return Generator<int, array<int, bool>> */
    public function provideOneLineOrNot(): iterable
    {
        yield [true];
        yield [false];
    }

    /**
     * @dataProvider provideOneLineOrNot
     *
     * @return void
     */
    public function testComplexTest(bool $oneLine)
    {
        $reader = new Cfonb120Reader();
        $statements = $reader->parse($this->loadFixture('complex-test.txt', $oneLine));

        self::assertCount(8, $statements);

        //Test first statement
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

        //Test second statement
        $statement = $statements[1];

        self::assertSame(16672.86, $statement->getOldBalance()->getAmount());
        self::assertCount(2, $statement->getOperations());
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

        //Third statement
        $statement = $statements[2];

        self::assertSame(11652.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11652.75, $statement->getNewBalance()->getAmount());

        //Test fourth statement
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

        //5th statement
        $statement = $statements[4];

        self::assertSame(11535.0, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11535.0, $statement->getNewBalance()->getAmount());

        //6th statement
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

        //7th statement
        $statement = $statements[6];

        self::assertSame(11484.75, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(11484.75, $statement->getNewBalance()->getAmount());

        //8th statement
        $statement = $statements[7];

        self::assertSame(584353.02, $statement->getOldBalance()->getAmount());
        self::assertCount(0, $statement->getOperations());
        self::assertSame(584353.02, $statement->getNewBalance()->getAmount());
    }
}

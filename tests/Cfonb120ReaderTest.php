<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) Guillaume Sainthillier <hello@silarhi.fr>
 * (c) @fezfez <demonchaux.stephane@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Siarhi\Cfonb\Tests;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

class Cfonb120ReaderTest extends TestCase
{
    /** @return void */
    public function testEmpty()
    {
        $reader = new Cfonb120Reader();

        $this->assertCount(0, $reader->parse(''));
    }

    /** @return void */
    public function testFailUnknowLine()
    {
        $reader = new Cfonb120Reader();

        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        $reader->parse('abc ');
    }

    /** @return void */
    public function testFailCauseNoOperation()
    {
        $reader = new Cfonb120Reader();

        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to attach a detail for operation with internal code 6772');
        $reader->parse('0510556677202204EUR2 00012345603B1070420     LIBDEDIBOX 3706114                                                         ');
    }

    public function provideMalformedLine(): array
    {
        return [
            'dateMalformed' => ['0110278    02204EUR2 00012345603  YYYYYY                                                  0000000166956E060420070420    '],
            'amountMalformed' => ['0110278    02204EUR2 00012345603  101020                                                  0000000166956Y060420070420    ']
        ];
    }

    /**
     * @dataProvider provideMalformedLine
     * @return void
     */
    public function testMalformedLine(string $line)
    {
        $reader = new Cfonb120Reader();

        self::expectException(ParseException::class);
        self::expectExceptionMessage('Regex does not match the line');
        $reader->parse($line);
    }

    /** @return void */
    public function testSimpleTest()
    {
        $reader     = new Cfonb120Reader();
        $statements = $reader->parse($this->loadFixture('simple-test.txt'));

        $this->assertCount(1, $statements);

        $statement = $statements[0];

        assert($statement instanceof Statement);

        $oldBalance = $statement->getOldBalance();

        $this->assertNotNull($oldBalance);

        assert($oldBalance instanceof Balance);

        $this->assertEquals(new \DateTime('2020-04-03'), $oldBalance->getDate());
        $this->assertEquals('EUR', $oldBalance->getCurrencyCode());
        $this->assertEquals(16695.65, $oldBalance->getAmount());
        $this->assertEquals('10278', $oldBalance->getBankCode());
        $this->assertEquals('02204', $oldBalance->getDeskCode());
        $this->assertEquals('00012345603', $oldBalance->getAccountNumber());


        $newBalance = $statement->getNewBalance();

        //Data are the same expect date
        $this->assertNotNull($newBalance);

        assert($newBalance instanceof Balance);

        $this->assertEquals(new \DateTime('2020-04-06'), $newBalance->getDate());
        $this->assertEquals('EUR', $newBalance->getCurrencyCode());
        $this->assertEquals(16695.65, $newBalance->getAmount());
        $this->assertEquals('10278', $newBalance->getBankCode());
        $this->assertEquals('02204', $newBalance->getDeskCode());
        $this->assertEquals('00012345603', $newBalance->getAccountNumber());
    }

    /** @return void */
    public function testComplexTest()
    {
        $reader     = new Cfonb120Reader();
        $statements = $reader->parse($this->loadFixture('complex-test.txt'));

        $this->assertCount(8, $statements);

        //Test first statement
        $statement = $statements[0];

        assert($statement instanceof Statement);

        $this->assertEquals(16695.65, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertEquals(new \DateTime('2020-04-07'), $operation->getDate());
        $this->assertEquals(-22.79, $operation->getAmount());
        $this->assertEquals('PRLV SEPA ONLINE SAS', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('DEDIBOX 3706114', $operation->getDetailsOrThrowException()->getAdditionalInformations());

        $this->assertEquals(16672.86, $statement->getNewBalanceOrThrowException()->getAmount());

        //Test second statement
        $statement = $statements[1];

        assert($statement instanceof Statement);

        $this->assertEquals(16672.86, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(2, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertEquals(new \DateTime('2020-04-08'), $operation->getDate());
        $this->assertEquals(-20.11, $operation->getAmount());
        $this->assertEquals('PRLV SEPA FREE MOBILE', $operation->getLabel());
        $this->assertNull($operation->getDetails());

        $operation = $operations[1];

        assert($operation instanceof Operation);

        $this->assertEquals(new \DateTime('2020-04-08'), $operation->getDate());
        $this->assertEquals(-5000, $operation->getAmount());
        $this->assertEquals('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        $this->assertNull($operation->getDetails());

        $this->assertEquals(11652.75, $statement->getNewBalanceOrThrowException()->getAmount());

        //Third statement
        $statement = $statements[2];

        assert($statement instanceof Statement);

        $this->assertEquals(11652.75, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11652.75, $statement->getNewBalanceOrThrowException()->getAmount());

        //Test fourth statement
        $statement = $statements[3];

        assert($statement instanceof Statement);

        $this->assertEquals(11652.75, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertEquals(new \DateTime('2020-04-10'), $operation->getDate());
        $this->assertEquals(new \DateTime('2020-04-01'), $operation->getValueDate());
        $this->assertEquals(-117.75, $operation->getAmount());
        $this->assertEquals('FACTURE SGT20022040001692', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('DONT TVA 11 39EUR', $operation->getDetailsOrThrowException()->getAdditionalInformations());

        $this->assertEquals(11535, $statement->getNewBalanceOrThrowException()->getAmount());

        //5th statement
        $statement = $statements[4];

        assert($statement instanceof Statement);

        $this->assertEquals(11535, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11535, $statement->getNewBalanceOrThrowException()->getAmount());

        //6th statement
        $statement = $statements[5];

        assert($statement instanceof Statement);

        $this->assertEquals(11535, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertEquals(new \DateTime('2020-04-14'), $operation->getDate());
        $this->assertEquals(new \DateTime('2020-04-14'), $operation->getValueDate());
        $this->assertEquals(-50.25, $operation->getAmount());
        $this->assertEquals('PRLV SEPA OVH SAS', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('PAYMENT ORDER 124359169', $operation->getDetailsOrThrowException()->getAdditionalInformations());
        $this->assertCount(2, $operation->getAllDetails());

        $this->assertEquals(11484.75, $statement->getNewBalanceOrThrowException()->getAmount());

        //7th statement
        $statement = $statements[6];

        assert($statement instanceof Statement);

        $this->assertEquals(11484.75, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11484.75, $statement->getNewBalanceOrThrowException()->getAmount());

        //8th statement
        $statement = $statements[7];

        assert($statement instanceof Statement);

        $this->assertEquals(584353.02, $statement->getOldBalanceOrThrowException()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(584353.02, $statement->getNewBalanceOrThrowException()->getAmount());
    }

    private function loadFixture(string $file) : string
    {
        $result = file_get_contents(__DIR__ . '/fixtures/' . $file);

        if ($result === false) {
            throw new \RuntimeException(sprintf('unable to get %s', $file));
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

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

use function assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;
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

        self::expectException(ParseException::class);
        self::expectExceptionMessage('Regex does not match the line');
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
        $statements = $reader->parse($this->loadFixture('simple-test.txt', $oneLine));

        $this->assertCount(1, $statements);

        $statement = $statements[0];

        assert($statement instanceof Statement);

        $this->assertTrue($statement->hasOldBalance());

        $this->assertSame('2020-04-03 00:00:00', $statement->getOldBalance()->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('EUR', $statement->getOldBalance()->getCurrencyCode());
        $this->assertSame(16695.65, $statement->getOldBalance()->getAmount());
        $this->assertSame('10278', $statement->getOldBalance()->getBankCode());
        $this->assertSame('02204', $statement->getOldBalance()->getDeskCode());
        $this->assertSame('00012345603', $statement->getOldBalance()->getAccountNumber());

        //Data are the same expect date
        $this->assertTrue($statement->hasNewBalance());

        $this->assertSame('2020-04-06 00:00:00', $statement->getNewBalance()->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('EUR', $statement->getNewBalance()->getCurrencyCode());
        $this->assertSame(16695.65, $statement->getNewBalance()->getAmount());
        $this->assertSame('10278', $statement->getNewBalance()->getBankCode());
        $this->assertSame('02204', $statement->getNewBalance()->getDeskCode());
        $this->assertSame('00012345603', $statement->getNewBalance()->getAccountNumber());
    }

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

        $this->assertCount(8, $statements);

        //Test first statement
        $statement = $statements[0];

        assert($statement instanceof Statement);

        $this->assertSame(16695.65, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertSame('2020-04-07 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-22.79, $operation->getAmount());
        $this->assertSame('PRLV SEPA ONLINE SAS', $operation->getLabel());
        $this->assertCount(1, $operation->getDetails());
        $this->assertSame('DEDIBOX 3706114', $operation->getDetails()[0]->getAdditionalInformations());

        $this->assertSame(16672.86, $statement->getNewBalance()->getAmount());

        //Test second statement
        $statement = $statements[1];

        assert($statement instanceof Statement);

        $this->assertSame(16672.86, $statement->getOldBalance()->getAmount());
        $this->assertCount(2, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-20.11, $operation->getAmount());
        $this->assertSame('PRLV SEPA FREE MOBILE', $operation->getLabel());
        $this->assertCount(0, $operation->getDetails());

        $operation = $operations[1];

        assert($operation instanceof Operation);

        $this->assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-5000.0, $operation->getAmount());
        $this->assertSame('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        $this->assertCount(0, $operation->getDetails());

        $this->assertSame(11652.75, $statement->getNewBalance()->getAmount());

        //Third statement
        $statement = $statements[2];

        assert($statement instanceof Statement);

        $this->assertSame(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11652.75, $statement->getNewBalance()->getAmount());

        //Test fourth statement
        $statement = $statements[3];

        assert($statement instanceof Statement);

        $this->assertSame(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        assert($operation instanceof Operation);

        $this->assertSame('2020-04-10 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('2020-04-01 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-117.75, $operation->getAmount());
        $this->assertSame('FACTURE SGT20022040001692', $operation->getLabel());
        $this->assertCount(1, $operation->getDetails());
        $this->assertSame('DONT TVA 11 39EUR', $operation->getDetails()[0]->getAdditionalInformations());

        $this->assertSame(11535.0, $statement->getNewBalance()->getAmount());

        //5th statement
        $statement = $statements[4];

        assert($statement instanceof Statement);

        $this->assertSame(11535.0, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11535.0, $statement->getNewBalance()->getAmount());

        //6th statement
        $statement = $statements[5];

        assert($statement instanceof Statement);

        $this->assertSame(11535.0, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertSame('2020-04-14 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('2020-04-14 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-50.25, $operation->getAmount());
        $this->assertSame('PRLV SEPA OVH SAS', $operation->getLabel());
        $this->assertCount(2, $operation->getDetails());
        $this->assertSame('PAYMENT ORDER 124359169', $operation->getDetails()[0]->getAdditionalInformations());

        $this->assertSame(11484.75, $statement->getNewBalance()->getAmount());

        //7th statement
        $statement = $statements[6];

        assert($statement instanceof Statement);

        $this->assertSame(11484.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11484.75, $statement->getNewBalance()->getAmount());

        //8th statement
        $statement = $statements[7];

        assert($statement instanceof Statement);

        $this->assertSame(584353.02, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(584353.02, $statement->getNewBalance()->getAmount());
    }

    private function loadFixture(string $file, bool $oneline): string
    {
        $result = file_get_contents(__DIR__ . '/fixtures/' . $file);

        if (false === $result) {
            throw new RuntimeException(sprintf('unable to get %s', $file));
        }

        if (true == $oneline) {
            $result = str_replace("\n", '', $result);
        }

        return $result;
    }
}

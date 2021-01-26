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
use RuntimeException;
use Silarhi\Cfonb\CfonbParser;
use Silarhi\Cfonb\Contracts\Cfonb120\OperationInterface;
use Silarhi\Cfonb\Contracts\Cfonb120\StatementInterface;
use Silarhi\Cfonb\Exceptions\ParseException;

class Cfonb120ReaderTest extends TestCase
{

    /**
     * @var CfonbParser
     */
    private $cfonbParser;

    public function setUp(): void
    {
        parent::setUp();
        $this->cfonbParser = new CfonbParser();
    }

    /** @return void */
    public function testEmpty()
    {
        $this->assertCount(0, $this->cfonbParser->read120C(''));
    }

    /** @return void */
    public function testFailUnknownLine()
    {
        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        $this->cfonbParser->read120C('abc ');
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
     *
     * @param string $line
     *
     * @return void
     */
    public function testMalformedLine(string $line)
    {
        self::expectException(ParseException::class);
        self::expectExceptionMessage('Regex does not match the line');
        $this->cfonbParser->read120C($line);
    }

    /**
     * @dataProvider provideOneLineOrNot
     *
     * @param bool $oneLine
     *
     * @return void
     */
    public function testSimpleTest(bool $oneLine)
    {
        $statements = $this->cfonbParser->read120C($this->loadFixture('cfonb.120-simple-test.txt', $oneLine));

        $this->assertCount(1, $statements);

        $statement = $statements[0];

        $this->assertInstanceOf(StatementInterface::class, $statement);

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
     * @param bool $oneLine
     *
     * @return void
     */
    public function testComplexTest(bool $oneLine)
    {
        $statements = $this->cfonbParser->read120C($this->loadFixture('cfonb.120-complex-test.txt', $oneLine));

        $this->assertCount(8, $statements);

        //Test first statement
        $statement = $statements[0];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(16695.65, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        $this->assertInstanceOf(OperationInterface::class, $operation);

        $this->assertSame('2020-04-07 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-22.79, $operation->getAmount());
        $this->assertSame('PRLV SEPA ONLINE SAS', $operation->getLabel());
        $this->assertCount(1, $operation->getDetails());
        $this->assertSame('DEDIBOX 3706114', $operation->getDetails()[0]->getAdditionalInformation());

        $this->assertSame(16672.86, $statement->getNewBalance()->getAmount());

        //Test second statement
        $statement = $statements[1];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(16672.86, $statement->getOldBalance()->getAmount());
        $this->assertCount(2, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        $this->assertInstanceOf(OperationInterface::class, $operation);

        $this->assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-20.11, $operation->getAmount());
        $this->assertSame('PRLV SEPA FREE MOBILE', $operation->getLabel());
        $this->assertCount(0, $operation->getDetails());

        $operation = $operations[1];

        $this->assertInstanceOf(OperationInterface::class, $operation);

        $this->assertSame('2020-04-08 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-5000.0, $operation->getAmount());
        $this->assertSame('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        $this->assertCount(0, $operation->getDetails());

        $this->assertSame(11652.75, $statement->getNewBalance()->getAmount());

        //Third statement
        $statement = $statements[2];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11652.75, $statement->getNewBalance()->getAmount());

        //Test fourth statement
        $statement = $statements[3];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];

        $this->assertInstanceOf(OperationInterface::class, $operation);

        $this->assertSame('2020-04-10 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('2020-04-01 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-117.75, $operation->getAmount());
        $this->assertSame('FACTURE SGT20022040001692', $operation->getLabel());
        $this->assertCount(1, $operation->getDetails());
        $this->assertSame('DONT TVA 11 39EUR', $operation->getDetails()[0]->getAdditionalInformation());

        $this->assertSame(11535.0, $statement->getNewBalance()->getAmount());

        //5th statement
        $statement = $statements[4];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(11535.0, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11535.0, $statement->getNewBalance()->getAmount());

        //6th statement
        $statement = $statements[5];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(11535.0, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertSame('2020-04-14 00:00:00', $operation->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('2020-04-14 00:00:00', $operation->getValueDate()->format('Y-m-d H:i:s'));
        $this->assertSame(-50.25, $operation->getAmount());
        $this->assertSame('PRLV SEPA OVH SAS', $operation->getLabel());
        $this->assertCount(2, $operation->getDetails());
        $this->assertSame('PAYMENT ORDER 124359169', $operation->getDetails()[0]->getAdditionalInformation());

        $this->assertSame(11484.75, $statement->getNewBalance()->getAmount());

        //7th statement
        $statement = $statements[6];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(11484.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(11484.75, $statement->getNewBalance()->getAmount());

        //8th statement
        $statement = $statements[7];

        $this->assertInstanceOf(StatementInterface::class, $statement);

        $this->assertSame(584353.02, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertSame(584353.02, $statement->getNewBalance()->getAmount());
    }

    private function loadFixture(string $file, bool $oneLine): string
    {
        $result = file_get_contents(__DIR__ . '/fixtures/' . $file);

        if ($result === false) {
            throw new RuntimeException(sprintf('unable to get %s', $file));
        }

        if ($oneLine == true) {
            $result = str_replace("\n", '', $result);
        }

        return $result;
    }
}

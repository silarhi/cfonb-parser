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
use Silarhi\Cfonb\Cfonb120Reader;

class Cfonb120ReaderTest extends TestCase
{
    public function testSimpleTest()
    {
        $reader = new Cfonb120Reader();
        $reader->parse($this->loadFixture('simple-test.txt'));

        $statements = $reader->getStatements();
        $this->assertCount(1, $statements);

        $statement = $statements[0];
        $this->assertNotNull($statement->getOldBalance());
        $this->assertEquals(new \DateTime('2020-04-03'), $statement->getOldBalance()->getDate());
        $this->assertEquals('EUR', $statement->getOldBalance()->getCurrencyCode());
        $this->assertEquals(16695.65, $statement->getOldBalance()->getAmount());
        $this->assertEquals('10278', $statement->getOldBalance()->getBankCode());
        $this->assertEquals('02204', $statement->getOldBalance()->getDeskCode());
        $this->assertEquals('00012345603', $statement->getOldBalance()->getAccountNumber());

        //Data are the same expect date
        $this->assertNotNull($statement->getNewBalance());
        $this->assertEquals(new \DateTime('2020-04-06'), $statement->getNewBalance()->getDate());
        $this->assertEquals('EUR', $statement->getNewBalance()->getCurrencyCode());
        $this->assertEquals(16695.65, $statement->getNewBalance()->getAmount());
        $this->assertEquals('10278', $statement->getNewBalance()->getBankCode());
        $this->assertEquals('02204', $statement->getNewBalance()->getDeskCode());
        $this->assertEquals('00012345603', $statement->getNewBalance()->getAccountNumber());
    }

    public function testComplexTest()
    {
        $reader = new Cfonb120Reader();
        $reader->parse($this->loadFixture('complex-test.txt'));

        $statements = $reader->getStatements();

        $this->assertCount(8, $statements);

        //Test first statement
        $statement = $statements[0];
        $this->assertEquals(16695.65, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertEquals(new \DateTime('2020-04-07'), $operation->getDate());
        $this->assertEquals(-22.79, $operation->getAmount());
        $this->assertEquals('PRLV SEPA ONLINE SAS', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('DEDIBOX 3706114', $operation->getDetails()->getAdditionalInformations());

        $this->assertEquals(16672.86, $statement->getNewBalance()->getAmount());

        //Test second statement
        $statement = $statements[1];
        $this->assertEquals(16672.86, $statement->getOldBalance()->getAmount());
        $this->assertCount(2, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertEquals(new \DateTime('2020-04-08'), $operation->getDate());
        $this->assertEquals(-20.11, $operation->getAmount());
        $this->assertEquals('PRLV SEPA FREE MOBILE', $operation->getLabel());
        $this->assertNull($operation->getDetails());

        $operation = $operations[1];
        $this->assertEquals(new \DateTime('2020-04-08'), $operation->getDate());
        $this->assertEquals(-5000, $operation->getAmount());
        $this->assertEquals('VIR JOHNDOE / FOOBAR', $operation->getLabel());
        $this->assertNull($operation->getDetails());

        $this->assertEquals(11652.75, $statement->getNewBalance()->getAmount());

        //Third statement
        $statement = $statements[2];
        $this->assertEquals(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11652.75, $statement->getNewBalance()->getAmount());

        //Test fourth statement
        $statement = $statements[3];
        $this->assertEquals(11652.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertEquals(new \DateTime('2020-04-10'), $operation->getDate());
        $this->assertEquals(new \DateTime('2020-04-01'), $operation->getValueDate());
        $this->assertEquals(-117.75, $operation->getAmount());
        $this->assertEquals('FACTURE SGT20022040001692', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('DONT TVA 11 39EUR', $operation->getDetails()->getAdditionalInformations());

        $this->assertEquals(11535, $statement->getNewBalance()->getAmount());

        //5th statement
        $statement = $statements[4];
        $this->assertEquals(11535, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11535, $statement->getNewBalance()->getAmount());

        //6th statement
        $statement = $statements[5];
        $this->assertEquals(11535, $statement->getOldBalance()->getAmount());
        $this->assertCount(1, $statement->getOperations());
        $operations = $statement->getOperations();

        $operation = $operations[0];
        $this->assertEquals(new \DateTime('2020-04-14'), $operation->getDate());
        $this->assertEquals(new \DateTime('2020-04-14'), $operation->getValueDate());
        $this->assertEquals(-50.25, $operation->getAmount());
        $this->assertEquals('PRLV SEPA OVH SAS', $operation->getLabel());
        $this->assertNotNull($operation->getDetails());
        $this->assertEquals('PAYMENT ORDER 124359169', $operation->getDetails()->getAdditionalInformations());
        $this->assertCount(2, $operation->getAllDetails());

        $this->assertEquals(11484.75, $statement->getNewBalance()->getAmount());

        //7th statement
        $statement = $statements[6];
        $this->assertEquals(11484.75, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(11484.75, $statement->getNewBalance()->getAmount());

        //8th statement
        $statement = $statements[7];
        $this->assertEquals(584353.02, $statement->getOldBalance()->getAmount());
        $this->assertCount(0, $statement->getOperations());
        $this->assertEquals(584353.02, $statement->getNewBalance()->getAmount());
    }

    private function loadFixture($file)
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $file);
    }
}

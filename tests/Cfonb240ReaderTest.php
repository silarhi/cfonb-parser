<?php

/*
 * This file is part of the CFONB Parser package.
 *
 * (c) AndrewSvirin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Siarhi\Cfonb\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Silarhi\Cfonb\CfonbParser;
use Silarhi\Cfonb\Exceptions\ParseException;

class Cfonb240ReaderTest extends TestCase
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
        $this->assertCount(0, $this->cfonbParser->read240C(''));
    }

    /** @return void */
    public function testFailUnknownLine()
    {
        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        $this->cfonbParser->read240C('abc ');
    }

    /**
     * @return void
     */
    public function testComplexTest()
    {
        $transactions = $this->cfonbParser->read240C($this->loadFixture('cfonb.240-complex-test.txt', false));

        $this->assertEquals(2, count($transactions));

        $transaction = reset($transactions);

        $this->assertEquals(2, count($transaction->getOperations()));

        return;
    }

    private function loadFixture(string $file, bool $oneline): string
    {
        $result = file_get_contents(__DIR__ . '/fixtures/' . $file);

        if ($result === false) {
            throw new RuntimeException(sprintf('unable to get %s', $file));
        }

        if ($oneline == true) {
            $result = str_replace("\n", '', $result);
        }

        return $result;
    }
}

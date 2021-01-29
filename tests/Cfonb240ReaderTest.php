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
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\Exceptions\ParseException;

class Cfonb240ReaderTest extends TestCase
{
    /** @return void */
    public function testEmpty()
    {
        $this->assertCount(0, (new Cfonb240Reader())->parse(''));
    }

    /** @return void */
    public function testFailUnknownLine()
    {
        self::expectException(ParseException::class);
        self::expectExceptionMessage('Unable to find a parser for the line :
"abc "');
        (new Cfonb240Reader())->parse('abc ');
    }

    /**
     * @return void
     */
    public function testComplexTest()
    {
        $transfers = (new Cfonb240Reader())->parse($this->loadFixture('cfonb.240-complex-test.txt', false));

        self::assertCount(2, $transfers);
        self::assertContainsOnlyInstancesOf(Transfer::class, $transfers);

        $firstTransfers = $transfers[0];

        assert($firstTransfers instanceof Transfer);

        self::assertCount(2, $firstTransfers->getTransactions());
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

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

use PHPUnit\Framework\Attributes\CoversClass;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Banking\Transfer;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\CfonbReader;
use Silarhi\Cfonb\Exceptions\ParseException;

#[CoversClass(CfonbReader::class)]
final class CfonbReaderTest extends CfonbTestCase
{
    public function testOk(): void
    {
        $cfon120 = $this->createMock(Cfonb120Reader::class);
        $cfon240 = $this->createMock(Cfonb240Reader::class);

        $cfon120->expects(self::once())->method('parse')->with('120!')->willReturn([]);
        $cfon240->expects(self::once())->method('parse')->with('240!')->willReturn([]);

        $sUT = new CfonbReader($cfon120, $cfon240);

        $sUT->parseCfonb120('120!');
        $sUT->parseCfonb240('240!');
    }

    public function testParseDetectsTheFormat(): void
    {
        $sUT = new CfonbReader();

        $statements = $sUT->parse(self::loadFixture('complex-test.txt', false));
        self::assertNotEmpty($statements);
        self::assertContainsOnlyInstancesOf(Statement::class, $statements);

        $transfers = $sUT->parse(self::loadFixture('cfonb.240-complex-test.txt', true));
        self::assertNotEmpty($transfers);
        self::assertContainsOnlyInstancesOf(Transfer::class, $transfers);

        self::assertSame([], $sUT->parse(''));
        self::assertSame([], $sUT->parse("\r\n"));
    }

    public function testParsePassesStrict(): void
    {
        $cfon120 = $this->createMock(Cfonb120Reader::class);
        $cfon120->expects(self::once())->method('parse')->with('01...', false)->willReturn([]);

        (new CfonbReader($cfon120))->parse('01...', false);
    }

    public function testParseUnknownFormat(): void
    {
        try {
            (new CfonbReader())->parse('99');
            self::fail('A ParseException should be thrown');
        } catch (ParseException $e) {
            self::assertStringContainsString('Unable to detect the CFONB format: the record code "99" of the first line', $e->getMessage());
        }
    }
}

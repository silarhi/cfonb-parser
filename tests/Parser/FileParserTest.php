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

namespace Siarhi\Cfonb\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Parser\FileParser;
use stdClass;

class FileParserTest extends TestCase
{
    /** @return void */
    public function testEmpty()
    {
        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::never())->method('supports');

        $sUT = new FileParser($parser);

        self::assertSame([], iterator_to_array($sUT->parse('', 10)));
    }

    /** @return void */
    public function testSplitOk()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $object3 = new stdClass();

        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::exactly(3))
            ->method('supports')
            ->withConsecutive(['aaaaaaaaaa'], ['bbbbbbbbbb'], [''])
            ->willReturn(true);
        $parser->expects(self::exactly(3))
            ->method('parse')
            ->withConsecutive(['aaaaaaaaaa'], ['bbbbbbbbbb'], [''])
            ->willReturnOnConsecutiveCalls($object1, $object2, $object3);

        $sUT = new FileParser($parser);

        self::assertSame([$object1, $object2, $object3], iterator_to_array($sUT->parse('aaaaaaaaaabbbbbbbbbb', 10)));
    }

    /** @return void */
    public function testDontSplitWithSameLength()
    {
        $object1 = new stdClass();

        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::exactly(1))
            ->method('supports')
            ->withConsecutive(['aaaaaaaaaa'])
            ->willReturn(true);
        $parser->expects(self::exactly(1))
            ->method('parse')
            ->withConsecutive(['aaaaaaaaaa'])
            ->willReturnOnConsecutiveCalls($object1);

        $sUT = new FileParser($parser);

        self::assertSame([$object1], iterator_to_array($sUT->parse('aaaaaaaaaa', 10)));
    }
}

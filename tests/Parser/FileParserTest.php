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

namespace Silarhi\Cfonb\Tests\Parser;

use function assert;
use function is_array;
use function is_bool;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Noop;
use Silarhi\Cfonb\Contracts\ParserInterface;
use Silarhi\Cfonb\Parser\FileParser;

class FileParserTest extends TestCase
{
    public function testEmpty(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::never())->method('supports');

        $sUT = new FileParser($parser);

        self::assertSame([], iterator_to_array($sUT->parse('', 10)));
    }

    public function testSplitOk(): void
    {
        $object1 = new Noop();
        $object2 = new Noop();
        $object3 = new Noop();

        $parser = $this->createMock(ParserInterface::class);

        $supportsSeries = [
            [['aaaaaaaaaa'], true],
            [['bbbbbbbbbb'], true],
            [[''], true],
        ];
        $parser->expects(self::exactly(3))
            ->method('supports')
            ->willReturnCallback(function (mixed ...$args) use (&$supportsSeries): bool {
                assert(is_array($supportsSeries));
                $serie = array_shift($supportsSeries);
                assert(is_array($serie));
                [$expectedArgs, $return] = $serie;
                assert(is_bool($return));
                $this->assertSame($expectedArgs, $args);

                return $return;
            })
        ;

        $parseSeries = [
            [['aaaaaaaaaa'], $object1],
            [['bbbbbbbbbb'], $object2],
            [[''], $object3],
        ];
        $parser->expects(self::exactly(3))
            ->method('parse')
            ->willReturnCallback(function (mixed ...$args) use (&$parseSeries): Noop {
                assert(is_array($parseSeries));
                $serie = array_shift($parseSeries);
                assert(is_array($serie));
                [$expectedArgs, $return] = $serie;
                assert($return instanceof Noop);
                $this->assertSame($expectedArgs, $args);

                return $return;
            })
        ;

        $sUT = new FileParser($parser);

        self::assertSame([$object1, $object2, $object3], iterator_to_array($sUT->parse('aaaaaaaaaabbbbbbbbbb', 10)));
    }

    public function testDontSplitWithSameLength(): void
    {
        $object1 = new Noop();

        $supportsSeries = [
            [['aaaaaaaaaa'], true],
        ];
        $parser = $this->createMock(ParserInterface::class);
        $parser->expects(self::exactly(1))
            ->method('supports')
            ->willReturnCallback(function (mixed ...$args) use (&$supportsSeries): bool {
                assert(is_array($supportsSeries));
                $serie = array_shift($supportsSeries);
                assert(is_array($serie));
                [$expectedArgs, $return] = $serie;
                assert(is_bool($return));
                $this->assertSame($expectedArgs, $args);

                return $return;
            })
        ;

        $parseSeries = [
            [['aaaaaaaaaa'], $object1],
        ];
        $parser->expects(self::exactly(1))
            ->method('parse')
            ->willReturnCallback(function (mixed ...$args) use (&$parseSeries): Noop {
                assert(is_array($parseSeries));
                $serie = array_shift($parseSeries);
                assert(is_array($serie));
                [$expectedArgs, $return] = $serie;
                assert($return instanceof Noop);
                $this->assertSame($expectedArgs, $args);

                return $return;
            });

        $sUT = new FileParser($parser);

        self::assertSame([$object1], iterator_to_array($sUT->parse('aaaaaaaaaa', 10)));
    }
}

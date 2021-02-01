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

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Cfonb120Reader;
use Silarhi\Cfonb\Cfonb240Reader;
use Silarhi\Cfonb\CfonbReader;

/** @covers Silarhi\Cfonb\CfonbReader */
class CfonbReaderTest extends TestCase
{
    /** @return void */
    public function testOk()
    {
        $cfon120 = self::createMock(Cfonb120Reader::class);
        $cfon240 = self::createMock(Cfonb240Reader::class);

        $cfon120->expects(self::once())->method('parse')->with('120!')->willReturn([]);
        $cfon240->expects(self::once())->method('parse')->with('240!')->willReturn([]);

        $sUT = new CfonbReader($cfon120, $cfon240);

        $sUT->parseCfonb120('120!');
        $sUT->parseCfonb240('240!');
    }
}

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

namespace Silarhi\Cfonb\Tests\Banking;

use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\DetailQualifier;

final class DetailQualifierTest extends TestCase
{
    public function testLabels(): void
    {
        self::assertSame('Nom du payeur', DetailQualifier::from('NPY')->label());
        self::assertSame(DetailQualifier::Label, DetailQualifier::from('LIB'));

        $labels = array_map(static fn (DetailQualifier $qualifier): string => $qualifier->label(), DetailQualifier::cases());
        self::assertCount(18, array_unique($labels));
    }
}

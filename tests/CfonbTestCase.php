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

use Override;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function sprintf;

abstract class CfonbTestCase extends TestCase
{
    /** @var list<string> the temporary files of the test, deleted after it */
    private array $temporaryFiles = [];

    /** A temporary file holding the content, deleted after the test */
    protected function temporaryFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'cfonb');
        if (false === $path || false === file_put_contents($path, $content)) {
            throw new RuntimeException('unable to write a temporary file');
        }
        $this->temporaryFiles[] = $path;

        return $path;
    }

    #[Override]
    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            @unlink($path);
        }
        $this->temporaryFiles = [];

        parent::tearDown();
    }

    public static function loadFixture(string $file, bool $oneline): string
    {
        $result = file_get_contents(__DIR__ . '/fixtures/' . $file);

        if (false === $result) {
            throw new RuntimeException(sprintf('unable to get %s', $file));
        }

        if ($oneline) {
            return str_replace("\n", '', $result);
        }

        return $result;
    }
}

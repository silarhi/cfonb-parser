<?php

namespace Silarhi\Cfonb\Tests\Models;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Models\Cfonb120\Detail;

class DetailTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $date = new DateTimeImmutable();

        $sUT = new Detail(
            'test',
            'test2',
            'test3',
            'test4',
            $date,
            'test5',
            'test6',
            'test7',
            'test8'
        );

        $this->assertSame('test', $sUT->getBankCode());
        $this->assertSame('test2', $sUT->getDeskCode());
        $this->assertSame('test3', $sUT->getAccountNumber());
        $this->assertSame('test4', $sUT->getCode());
        $this->assertSame($date, $sUT->getDate());
        $this->assertSame('test5', $sUT->getQualifier());
        $this->assertSame('test6', $sUT->getAdditionalInformation());
        $this->assertSame('test7', $sUT->getInternalCode());
       $this->assertSame('test8', $sUT->getCurrencyCode());
    }
}
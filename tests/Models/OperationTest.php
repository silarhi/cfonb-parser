<?php


namespace Silarhi\Cfonb\Tests\Models;


use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Models\Cfonb120\Detail;
use Silarhi\Cfonb\Models\Cfonb120\Operation;

class OperationTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $date = new DateTimeImmutable();
        $valueDate = new DateTimeImmutable();

        $sUT = new Operation(
            'test',
            'test2',
            'test3',
            'test4',
            $date,
            $valueDate,
            'test5',
            'test6',
            10.0,
            'test7',
            'test8',
            'test9',
            'test10'
        );

        $this->assertSame('test', $sUT->getBankCode());
        $this->assertSame('test2', $sUT->getDeskCode());
        $this->assertSame('test3', $sUT->getAccountNumber());
        $this->assertSame('test4', $sUT->getCode());
        $this->assertSame($date, $sUT->getDate());
        $this->assertSame($valueDate, $sUT->getValueDate());
        $this->assertSame('test5', $sUT->getLabel());
        $this->assertSame('test6', $sUT->getReference());
        $this->assertSame(10.0, $sUT->getAmount());
        $this->assertSame('test7', $sUT->getInternalCode());
        $this->assertSame('test8', $sUT->getCurrencyCode());
        $this->assertSame('test9', $sUT->getRejectCode());
        $this->assertSame('test10', $sUT->getExemptCode());
        $this->assertCount(0, $sUT->getDetails());

        $detail = $this->createMock(Detail::class);

        $sUT->addDetail($detail);

        $this->assertCount(1, $sUT->getDetails());
        $this->assertSame([$detail], $sUT->getDetails());
    }
}
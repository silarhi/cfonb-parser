<?php


namespace Silarhi\Cfonb\Tests\Banking;


use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;

class OperationTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $date = new \DateTimeImmutable();
        $valueDate = new \DateTimeImmutable();

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

        self::assertSame('test', $sUT->getBankCode());
        self::assertSame('test2', $sUT->getDeskCode());
        self::assertSame('test3', $sUT->getAccountNumber());
        self::assertSame('test4', $sUT->getCode());
        self::assertSame($date, $sUT->getDate());
        self::assertSame($valueDate, $sUT->getValueDate());
        self::assertSame('test5', $sUT->getLabel());
        self::assertSame('test6', $sUT->getReference());
        self::assertSame(10.0, $sUT->getAmount());
        self::assertSame('test7', $sUT->getInternalCode());
        self::assertSame('test8', $sUT->getCurrencyCode());
        self::assertSame('test9', $sUT->getRejectCode());
        self::assertSame('test10', $sUT->getExemptCode());
        self::assertNull($sUT->getDetails());

        $detail = self::createMock(OperationDetail::class);

        $sUT->setDetails($detail);

        self::assertSame($detail, $sUT->getDetails());
        self::assertSame($detail, $sUT->getDetailsOrThrowException());
        self::assertSame([$detail], $sUT->getAllDetails());
    }

    /** @return void */
    public function testFailGetDetailOrThrowExceptionFail()
    {
        $sUT = new Operation(
            'test',
            'test2',
            'test3',
            'test4',
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            'test5',
            'test6',
            10.0,
            'test7',
            'test8',
            'test9',
            'test10'
        );

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('detail is null');

        $sUT->getDetailsOrThrowException();
    }
}
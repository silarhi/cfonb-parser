<?php


namespace Silarhi\Cfonb\Tests\Banking;


use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Banking\Statement;

class StatementTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $sUT = new Statement();

        self::assertCount(0, $sUT->getOperations());
        self::assertNull($sUT->getNewBalance());
        self::assertNull($sUT->getOldBalance());

        $newBalance = self::createMock(Balance::class);
        $oldBalance = self::createMock(Balance::class);

        $sUT->setNewBalance($newBalance);
        $sUT->setOldBalance($oldBalance);

        self::assertSame($newBalance, $sUT->getNewBalance());
        self::assertSame($oldBalance, $sUT->getOldBalance());
        self::assertSame($newBalance, $sUT->getNewBalanceOrThrowException());
        self::assertSame($oldBalance, $sUT->getOldBalanceOrThrowException());
    }

    /** @return void */
    public function testFailGetOldBalanceOrThrowExceptionFail()
    {
        $sUT = new Statement();

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('old balance is null');

        $sUT->getOldBalanceOrThrowException();
    }

    /** @return void */
    public function testFailGetNewBalanceOrThrowExceptionFail()
    {
        $sUT = new Statement();

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('new balance is null');

        $sUT->getNewBalanceOrThrowException();
    }
}
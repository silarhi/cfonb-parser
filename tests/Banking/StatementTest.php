<?php


namespace Silarhi\Cfonb\Tests\Banking;


use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Banking\Balance;
use Silarhi\Cfonb\Banking\Operation;
use Silarhi\Cfonb\Banking\OperationDetail;
use Silarhi\Cfonb\Banking\Statement;
use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;

class StatementTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $sUT = new Statement();

        self::assertCount(0, $sUT->getOperations());
        self::assertFalse($sUT->hasNewBalance());
        self::assertFalse($sUT->hasOldBalance());

        $newBalance = self::createMock(Balance::class);
        $oldBalance = self::createMock(Balance::class);

        $sUT->setNewBalance($newBalance);
        $sUT->setOldBalance($oldBalance);

        self::assertTrue($sUT->hasNewBalance());
        self::assertTrue($sUT->hasOldBalance());

        self::assertSame($newBalance, $sUT->getNewBalance());
        self::assertSame($oldBalance, $sUT->getOldBalance());
    }

    /** @return void */
    public function testFailGetOldBalance()
    {
        $sUT = new Statement();

        self::expectException(BalanceUnavailableException::class);
        self::expectExceptionMessage('old balance is null');

        $sUT->getOldBalance();
    }

    /** @return void */
    public function testFailGetNewBalance()
    {
        $sUT = new Statement();

        self::expectException(BalanceUnavailableException::class);
        self::expectExceptionMessage('new balance is null');

        $sUT->getNewBalance();
    }
}
<?php


namespace Silarhi\Cfonb\Tests\Models;


use PHPUnit\Framework\TestCase;
use Silarhi\Cfonb\Exceptions\BalanceUnavailableException;
use Silarhi\Cfonb\Models\Cfonb120\NewBalance;
use Silarhi\Cfonb\Models\Cfonb120\OldBalance;
use Silarhi\Cfonb\Models\Cfonb120\Statement;

class StatementTest  extends TestCase
{
    /** @return void */
    public function testGetter()
    {
        $sUT = new Statement();

        $this->assertCount(0, $sUT->getOperations());
        $this->assertFalse($sUT->hasNewBalance());
        $this->assertFalse($sUT->hasOldBalance());

        $newBalance = $this->createMock(NewBalance::class);
        $oldBalance = $this->createMock(OldBalance::class);

        $sUT->setNewBalance($newBalance);
        $sUT->setOldBalance($oldBalance);

        $this->assertTrue($sUT->hasNewBalance());
        $this->assertTrue($sUT->hasOldBalance());

        $this->assertSame($newBalance, $sUT->getNewBalance());
        $this->assertSame($oldBalance, $sUT->getOldBalance());
    }

    /** @return void */
    public function testFailGetOldBalance()
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('old balance is null');

        $sUT->getOldBalance();
    }

    /** @return void */
    public function testFailGetNewBalance()
    {
        $sUT = new Statement();

        $this->expectException(BalanceUnavailableException::class);
        $this->expectExceptionMessage('new balance is null');

        $sUT->getNewBalance();
    }
}
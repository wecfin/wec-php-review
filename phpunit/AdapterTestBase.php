<?php
namespace phpunit\Wec\Review;

use PHPUnit\Framework\TestCase;
use Gap\Db\Pdo\Param\ParamBase;
use Gap\Db\DbManager;
use Gap\Db\MySql\Cnn;

class AdapterTestBase extends TestCase
{
    protected $isbStub;
    protected $ssbStub;
    protected $cnn;

    protected function getDmgStub()
    {
        $dmgStub = $this->createMock(DbManager::class);

        $dmgStub->method('connect')
            ->willReturn($this->getCnn());

        return $dmgStub;
    }

    protected function getCnn()
    {
        if ($this->cnn) {
            return $this->cnn;
        }

        $pdo = $this->createMock('PDO');
        $stmt = $this->createMock('PDOStatement');
        $stmt->method('execute')->willReturn(true);

        $pdo->method('prepare')->willReturn($stmt);
        $serverId = 'gap-db';
        $this->cnn = new Cnn($pdo, $serverId);
        return $this->cnn;
    }

    protected function getCnnStub()
    {
        $cnnStub = $this->createMock(Mysql::class);
        $cnnStub->method('insert')
            ->willReturn($this->getIsbStub());

        $cnnStub->method('select')
            ->willReturn($this->getSsbStub());

        return $cnnStub;
    }

     /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initParamIndex(): void
    {
        ParamBase::initIndex();
    }
}

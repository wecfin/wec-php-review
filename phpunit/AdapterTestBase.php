<?php
namespace phpunit\Wec\Review;

use PHPUnit\Framework\TestCase;

use Gap\Database\DatabaseManager;
use Gap\Database\Connection\Mysql;
use Gap\Database\DataSet;

use phpunit\Wec\Review\Mock\InsertSqlBuilderMock;
use phpunit\Wec\Review\Mock\SelectSqlBuilderMock;

class AdapterTestBase extends TestCase
{
    protected $isbStub;
    protected $ssbStub;

    protected function getDmgStub()
    {
        $dmgStub = $this->createMock(DatabaseManager::class);

        $dmgStub->method('connect')
            ->willReturn($this->getCnnStub());

        return $dmgStub;
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

    protected function getIsbStub()
    {
        if ($this->isbStub) {
            return $this->isbStub;
        }

        $this->isbStub = new InsertSqlBuilderMock();
        return $this->isbStub;
    }

    protected function getSsbStub()
    {
        if ($this->ssbStub) {
            return $this->ssbStub;
        }

        $this->ssbStub = new SelectSqlBuilderMock();
        return $this->ssbStub;
    }
}

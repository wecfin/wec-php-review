<?php
namespace Wec\Review\Repo;

use Gap\Db\DbManager;
use Gap\Db\Pdo\Param\ParamBase;

class RepoBase
{
    protected $cnn;
    protected $dst;

    protected $dstKey;

    public function __construct(DbManager $dmg, string $database = 'default')
    {
        $this->cnn = $dmg->connect($database);
        if (empty($this->cnn)) {
            throw new \Exception("Cannot connect database: [$database]");
        }
    }

    public function setDst(string $dst): void
    {
        $this->dst = $dst;
    }

    protected function getDstKey(): string
    {
        if ($this->dstKey) {
            return $this->dstKey;
        }

        $this->dstKey = lcfirst(str_replace('_', '', ucwords($this->dst, '_'))) . 'Id';
        return $this->dstKey;
    }

    protected function getDst(): string
    {
        return $this->dst;
    }

    protected function initParamIndex(): void
    {
        ParamBase::initIndex();
    }
}

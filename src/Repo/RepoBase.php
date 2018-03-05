<?php
namespace Wec\Review\Repo;

use Gap\Database\DatabaseManager;

class RepoBase
{
    protected $cnn;
    protected $dst;

    protected $dstKey;

    public function __construct(DatabaseManager $dmg, string $database = 'default')
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

    protected function dataSet(SelectSqlBuilderMock $ssb, $dtoClass)
    {
        //这里第一个参数传过来是一个SelectSqlBuilderMock的class，
        //但是原本的dataSet方法是一个SelectSqlBuilderInterface的class
        //这个方法在repo里面 本身就不能在这里重写Mock的东西
        if ($this->dataSet) {
            return $this->dataSet;
        }

        $this->dataSet = new DataSetMock($ssb, $dtoClass);
        return $this->dataSet;
    }
}

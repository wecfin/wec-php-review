<?php
namespace Wec\Review\Repo;

use Gap\Database\DatabaseManager;

class RepoBase
{
    protected $cnn;
    protected $dst;

    public function __construct(DatabaseManager $dmg, string $database = 'default')
    {
        $this->cnn = $dmg->connect($database);
    }

    public function setDst(string $dst): void
    {
        $this->dst = $dst;
    }
}

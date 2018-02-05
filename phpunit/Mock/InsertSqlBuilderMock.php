<?php
namespace phpunit\Wec\Review\Mock;

class InsertSqlBuilderMock
{
    protected $vals = [];

    public function value(string $param, string $val)
    {
        $this->vals[$param] = $val;
        return $this;
    }

    public function execute(): void
    {
    }

    public function getVals(): array
    {
        return $this->vals;
    }
}

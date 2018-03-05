<?php
namespace phpunit\Wec\Review\Mock;

class DataSetMock extends
{
    protected $ssb;
    protected $dtoClass;

    public function __construct(SelectSqlBuilderMock $ssb, $dtoClass = '')
    {
        $this->ssb = $ssb;
        $this->dtoClass = $dtoClass;
    }
}

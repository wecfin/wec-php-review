<?php
namespace phpunit\Wec\Review\Mock;

use PHPUnit\Framework\TestCase;
use Wec\Review\Dto\ReviewerDto;

class SelectSqlBuilderMock extends TestCase
{
    public function from()
    {
        return $this;
    }

    public function where($dstKey, $dd, $dstId)
    {
        if ($dstKey === 'orderId' && $dd === '=' && $dstId === 'fakeOrderId') {
            return $this;
        }

        throw new \Exception('orderId invalid');
    }

    public function andWhere($dstKey, $dd, $dstId)
    {
        if ($dstKey === 'employeeId' && $dd === '=' && $dstId === 'fakeEmployeeId') {
            return $this;
        }

        throw new \Exception('employeeId invalid');
    }

    public function fetch()
    {
        return new ReviewerDto([
            'employeeId' => 'fakeEmployeeId',
            'fullName' => 'fakeFullName',
            'sequence' => 1,
            'created' => 'fakeCreated'
        ]);
    }
}

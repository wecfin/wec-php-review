<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewAdapterTest extends AdapterTestBase
{
    public function testApprove(): void
    {
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->approve($employeeId, $dstId, $message);

        $vals = $this->isbStub->getVals();

        $this->assertEquals('fakeDstId', $vals['orderId']);
        $this->assertEquals('fakeEmployeeId', $vals['employeeId']);
        $this->assertEquals('fakeMessage', $vals['message']);
    }

    public function testReject(): void
    {
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->approve($employeeId, $dstId, $message);

        $vals = $this->isbStub->getVals();

        $this->assertEquals('fakeDstId', $vals['orderId']);
        $this->assertEquals('fakeEmployeeId', $vals['employeeId']);
        $this->assertEquals('fakeMessage', $vals['message']);
    }

    public function testFetchReviewer()
    {
        $dstId = 'fakeOrderId';
        $employeeId = 'fakeEmployeeId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewer = $reviewAdapter->fetchReviewer($dstId, $employeeId);

        $this->assertEquals(
            new ReviewerDto([
                'employeeId' => 'fakeEmployeeId',
                'fullName' => 'fakeFullName',
                'sequence' => 1,
                'created' => 'fakeCreated'
            ]),
            $reviewer
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

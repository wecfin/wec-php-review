<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewAdapterTest extends AdapterTestBase
{
    public function testApprove(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->approve($employeeId, $dstId, $message);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();
        
        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, created) VALUES (:k1, :k2, :k3, :k4, :k5, :k6)',
            $sql
        );

        unset($vals[':k1']);
        unset($vals[':k6']);
        $this->assertEquals(
            [':k2' => $dstId, ':k3' => $employeeId, ':k4' => $message, ':k5' => 'approved'],
            $vals
        );
    }

    public function testReject(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->reject($employeeId, $dstId, $message);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, created) VALUES (:k1, :k2, :k3, :k4, :k5, :k6)',
            $sql
        );

        unset($vals[':k1']);
        unset($vals[':k6']);
        $this->assertEquals(
            [':k2' => $dstId, ':k3' => $employeeId, ':k4' => $message, ':k5' => 'rejected'],
            $vals
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

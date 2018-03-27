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
        $flow = 1;
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->approve($employeeId, $dstId, $message, $flow);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[1];
       
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, flow, created) VALUES (:k3, :k4, :k5, :k6, :k7, :k8, :k9)',
            $sql
        );

        unset($vals[':k3']);
        unset($vals[':k9']);
        $this->assertEquals(
            [':k4' => $dstId, ':k5' => $employeeId, ':k6' => $message, ':k7' => 'approved', ':k8' => $flow],
            $vals
        );
    }

    public function testReject(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $message = 'fakeMessage';
        $flow = 1;
        $type = 'order';

        $reviewAdapter = new ReviewAdapter($type, $this->getDmgStub());
        $reviewAdapter->reject($employeeId, $dstId, $message, $flow);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[1];
        
        $sql = $stmt->sql();
        
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, flow, created) VALUES (:k3, :k4, :k5, :k6, :k7, :k8, :k9)',
            $sql
        );

        unset($vals[':k3']);
        unset($vals[':k9']);
        $this->assertEquals(
            [':k4' => $dstId, ':k5' => $employeeId, ':k6' => $message, ':k7' => 'rejected', ':k8' => $flow],
            $vals
        );
    }

    public function testFetchReview(): void
    {
        $this->initParamIndex();
        
        $reviewId = 'fakeReviewId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->fetchReview($reviewId);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT * FROM order_review WHERE reviewId = :k1 LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $reviewId],
            $vals
        );
    }

    public function testListReview(): void
    {
        $this->initParamIndex();
        
        $dstId = 'fakeOrderId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewList = $reviewAdapter->listReview($dstId);
        $reviewList->rewind();

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT * FROM order_review WHERE orderId = :k1 ORDER BY created ASC LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId],
            $vals
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

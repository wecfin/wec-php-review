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
        $stmt = $executed[2];

        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, flow, created) VALUES (:k6, :k7, :k8, :k9, :k10, :k11, :k12)',
            $sql
        );

        unset($vals[':k6']);
        unset($vals[':k12']);
        $this->assertEquals(
            [':k7' => $dstId, ':k8' => $employeeId, ':k9' => $message, ':k10' => 'approved', ':k11' => $flow],
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
        $stmt = $executed[2];

        $sql = $stmt->sql();

        $vals = $stmt->vals();
        $this->assertEquals(
            'INSERT INTO order_review (reviewId, orderId, employeeId, message, result, flow, created) VALUES (:k6, :k7, :k8, :k9, :k10, :k11, :k12)',
            $sql
        );

        unset($vals[':k6']);
        unset($vals[':k12']);
        $this->assertEquals(
            [':k7' => $dstId, ':k8' => $employeeId, ':k9' => $message, ':k10' => 'rejected', ':k11' => $flow],
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
            'SELECT t.reviewId, t.employeeId, t.result, t.message, t.flow, t.created FROM order_review t WHERE reviewId = :k1 LIMIT 10',
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
            'SELECT t.reviewId, t.employeeId, t.result, t.message, t.flow, t.created FROM order_review t WHERE orderId = :k1 ORDER BY created ASC LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId],
            $vals
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

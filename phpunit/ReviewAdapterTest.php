<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewAdapterTest extends AdapterTestBase
{
    public function testFetchReview(): void
    {
        $this->initParamIndex();

        $reviewId = 'fakeReviewId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->fetchReviewByReviewId($reviewId);

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

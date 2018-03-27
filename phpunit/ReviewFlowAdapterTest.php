<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewFlowAdapterTest extends AdapterTestBase
{
    public function testFetchCurrentReviewFlow(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $dst = 'order';

        $reviewAdapter = new ReviewAdapter($dst, $this->getDmgStub());
        $reviewAdapter->fetchCurrentReviewFlow($dstId);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT * FROM review_flow WHERE dstType = :k1 AND dstId = :k2 ORDER BY created DESC LIMIT 1',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dst, ':k2' => $dstId],
            $vals
        );
    }

    public function testCreateReviewFlow(): void
    {
        $this->initParamIndex();
        $dstId = 'fakeDstId';
        $flow = 1;
        $dst = 'order';

        $reviewAdapter = new ReviewAdapter($dst, $this->getDmgStub());
        $reviewAdapter->createReviewFlow($dstId, $flow);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'INSERT INTO review_flow (reviewFlowId, dstType, dstId, flow, created) VALUES (:k1, :k2, :k3, :k4, :k5)',
            $sql
        );
        
        unset($vals[':k1']);
        unset($vals[':k5']);
        
        $this->assertEquals(
            [':k2' => $dst, ':k3' => $dstId, ':k4' => $flow],
            $vals
        );
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

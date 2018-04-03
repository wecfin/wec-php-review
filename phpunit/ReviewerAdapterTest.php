<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewerAdapterTest extends AdapterTestBase
{
    public function testFetchReviewer(): void
    {
        $this->initParamIndex();

        $dstId = 'fakeOrderId';
        $employeeId = 'fakeEmployeeId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->fetchReviewer($dstId, $employeeId);

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT t.employeeId, t.fullName, t.sequence, t.created FROM order_reviewer t WHERE orderId = :k1 AND employeeId = :k2 LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId, ':k2' => $employeeId],
            $vals
        );
    }

    public function testListReviewer(): void
    {
        $this->initParamIndex();

        $dstId = 'fakeOrderId';

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewerList = $reviewAdapter->listReviewer($dstId);
        $reviewerList->rewind();

        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];

        $sql = $stmt->sql();
        $vals = $stmt->vals();

        $this->assertEquals(
            'SELECT t.employeeId, t.fullName, t.sequence, t.created FROM order_reviewer t WHERE orderId = :k1 ORDER BY sequence ASC LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId],
            $vals
        );
    }
}

<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewerAdapterTest extends AdapterTestBase
{
    public function testAddReviewer(): void
    {
        $this->initParamIndex();
        
        $dstId = 'fakeDstId';
        $employeeId = 'fakeEmployeeId';
        $fullName = 'fakeFullName';
        $sequence = 1;

        $reviewer = new ReviewerDto([
            'employeeId' => $employeeId,
            'fullName' => $fullName,
            'sequence' => $sequence
        ]);

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->addReviewer($dstId, $reviewer);
        
        $executed = $this->getCnn()->executed();
        $stmt = $executed[0];
        
        $sql = $stmt->sql();
        $vals = $stmt->vals();
        
        $this->assertEquals(
            'INSERT INTO order_reviewer (orderId, employeeId, fullName, sequence, created) VALUES (:k1, :k2, :k3, :k4, :k5)',
            $sql
        );

        unset($vals[':k5']);
        $this->assertEquals(
            [':k1' => $dstId, ':k2' => $employeeId, ':k3' => $fullName, ':k4' => $sequence],
            $vals
        );
    }

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
            'SELECT * FROM order_reviewer WHERE orderId = :k1 AND employeeId = :k2 LIMIT 10',
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
            'SELECT * FROM order_reviewer WHERE orderId = :k1 ORDER BY sequence ASC LIMIT 10',
            $sql
        );

        $this->assertEquals(
            [':k1' => $dstId],
            $vals
        );
    }
}

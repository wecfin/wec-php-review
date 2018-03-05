<?php
namespace phpunit\Wec\Review;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

class ReviewerAdapterTest extends AdapterTestBase
{
    protected $isbStub;

    public function testAddReviewer(): void
    {
        $dstId = 'fakeDstId';
        $reviewer = new ReviewerDto([
            'employeeId' => 'fakeEmployeeId',
            'fullName' => 'fakeFullName',
            'sequence' => 1
        ]);

        $reviewAdapter = new ReviewAdapter('order', $this->getDmgStub());
        $reviewAdapter->addReviewer($dstId, $reviewer);

        $vals = $this->isbStub->getVals();

        $this->assertEquals('fakeDstId', $vals['orderId']);
        $this->assertEquals('fakeEmployeeId', $vals['employeeId']);
        $this->assertEquals('fakeFullName', $vals['fullName']);
        $this->assertEquals(1, $vals['sequence']);
    }
}

<?php
namespace phpunit\Wec\Review;

use PHPUnit\Framework\TestCase;

use Gap\Database\DatabaseManager;
use Gap\Database\Connection\Mysql;

use Wec\Review\ReviewAdapter;
use Wec\Review\Dto\ReviewerDto;

use phpunit\Wec\Review\Mock\InsertSqlBuilderMock;

class ReviewAdapterTest extends TestCase
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

    protected function getDmgStub()
    {
        $dmgStub = $this->createMock(DatabaseManager::class);

        $dmgStub->method('connect')
            ->willReturn($this->getCnnStub());

        return $dmgStub;
    }

    protected function getCnnStub()
    {
        $cnnStub = $this->createMock(Mysql::class);
        $cnnStub->method('insert')
            ->willReturn($this->getIsbStub());

        return $cnnStub;
    }

    protected function getIsbStub()
    {
        if ($this->isbStub) {
            return $this->isbStub;
        }

        $this->isbStub = new InsertSqlBuilderMock();
        return $this->isbStub;
    }
}

// https://phpunit.de/manual/current/en/test-doubles.html

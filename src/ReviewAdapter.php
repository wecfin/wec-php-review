<?php
namespace Wec\Review;

use Gap\Database\DatabaseManager;
use Gap\Database\DataSet;

use Wec\Review\Repo\ReviewRepo;
use Wec\Review\Repo\ReviewerRepo;
use Wec\Review\Dto\ReviewDto;
use Wec\Review\Dto\ReviewerDto;

class ReviewAdapter
{
    protected $dst;
    protected $dmg;
    protected $reviewRepo;
    protected $reviewerRepo;

    protected $reviewTable;
    protected $reviewerTable;
    protected $dstKey;

    public function __construct(string $dst, DatabaseManager $dmg, string $database = 'default')
    {
        $this->dst = $dst;
        $this->dmg = $dmg;

        $this->reviewRepo = new ReviewRepo($this->dmg, $database);
        $this->reviewerRepo = new ReviewerRepo($this->dmg, $database);

        $this->reviewRepo->setDst($this->dst);
        $this->reviewerRepo->setDst($this->dst);
    }

    public function reject(string $employeeId, string $dstId, string $message = ''): void
    {
    }

    public function approve(string $employeeId, string $dstId, string $message = ''): void
    {
    }

    public function verify(string $dstId): bool
    {
    }

    public function addReviewer(string $dstId, ReviewerDto $reviewer): void
    {
        $this->reviewerRepo->addReviewer($dstId, $reviewer);
    }

    public function listReviewer(string $dstId): DataSet
    {
    }

    public function fetchReviewer(string $dstId, string $employeeId): ReviewerDto
    {
    }

    public function listReview(string $dstId): DataSet
    {
    }

    public function fetchReview(string $reviewId): ReviewDto
    {
    }
}

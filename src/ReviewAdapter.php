<?php
namespace Wec\Review;

use Gap\Db\DbManager;
use Gap\Db\MySql\Collection;

use Wec\Review\Repo\ReviewRepo;
use Wec\Review\Repo\ReviewFlowRepo;
use Wec\Review\Repo\ReviewerRepo;
use Wec\Review\Dto\ReviewDto;
use Wec\Review\Dto\ReviewerDto;
use Wec\Review\Dto\ReviewFlowDto;

class ReviewAdapter
{
    protected $dst;
    protected $dmg;
    protected $reviewRepo;
    protected $reviewerRepo;

    protected $reviewTable;
    protected $reviewerTable;
    protected $dstKey;

    public function __construct(string $dst, DbManager $dmg, string $database = 'default')
    {
        $this->dst = $dst;
        $this->dmg = $dmg;

        $this->reviewRepo = new ReviewRepo($this->dmg, $database);
        $this->reviewerRepo = new ReviewerRepo($this->dmg, $database);
        $this->reviewFlowRepo = new ReviewFlowRepo($this->dmg, $database);

        $this->reviewRepo->setDst($this->dst);
        $this->reviewerRepo->setDst($this->dst);
        $this->reviewFlowRepo->setDst($this->dst);
    }

    public function approve(string $employeeId, string $dstId, string $message = ''): void
    {
        $flow = $this->reviewFlowRepo->fetchCurrentReviewFlow($dstId);
        $this->verifyReview($employeeId, $dstId, $flow);

        $this->reviewRepo->approve($employeeId, $dstId, $message, $flow);
    }

    public function reject(string $employeeId, string $dstId, string $message = ''): void
    {
        $flow = $this->reviewFlowRepo->fetchCurrentReviewFlow($dstId);
        $this->verifyReview($employeeId, $dstId, $flow);
        $this->reviewRepo->reject($employeeId, $dstId, $message, $flow);

        $flow++;
        $this->reviewFlowRepo->createReviewFlow($dstId, $flow);
    }

    public function listReviewer(string $dstId): Collection
    {
        return $this->reviewerRepo->listReviewer($dstId);
    }

    public function listReview(string $dstId): Collection
    {
        return $this->reviewRepo->listReview($dstId);
    }

    public function addReviewerList(string $dstId, array $reviewerList): void
    {
        $this->reviewerRepo->addReviewerList($dstId, $reviewerList);
    }

    public function abandonReview(string $dstId): void
    {
        $flow = $this->reviewFlowRepo->fetchCurrentReviewFlow($dstId);
        $latestReview = $this->reviewRepo->fetchLastestReview($dstId);

        if ($latestReview) {
            $flow++;
            $this->reviewFlowRepo->createReviewFlow($dstId, $flow);
        }
    }

    public function fetchReviewer(string $dstId, string $employeeId): ?ReviewerDto
    {
        return $this->reviewerRepo->fetchReviewer($dstId, $employeeId);
    }

    public function fetchReviewByReviewId(string $reviewId): ? ReviewDto
    {
        return $this->reviewRepo->fetchReviewByReviewId($reviewId);
    }

    protected function addReviewer(string $dstId, ReviewerDto $reviewer): void
    {
        $this->reviewerRepo->addReviewer($dstId, $reviewer);
    }

    protected function verifyReview(string $employeeId, string $dstId, int $flow)
    {
        $this->verifyIsLegalReviewer($employeeId, $dstId, $flow);
    }

    protected function verifyIsLegalReviewer(string $employeeId, string $dstId, int $flow)
    {
        $reviewer = $this->reviewerRepo->fetchReviewer($dstId, $employeeId);

        if (!$reviewer) {
            throw new \Exception('you are not in reviewerList');
        }

        $this->verifyIsRepeatSubmit($employeeId, $dstId, $flow);

        $sequence = $reviewer->sequence;
        
        $this->verifyReviewIsInCorrectSequence($sequence, $dstId, $flow);
    }

    protected function verifyIsRepeatSubmit(string $employeeId, string $dstId, int $flow): void
    {
        $appliedReview = $this->reviewRepo->fetchReviewByEmployeeIdInFlow($dstId, $employeeId, $flow);

        if ($appliedReview) {
            throw new \Exception('you have already reviewed');
        }
    }

    protected function verifyReviewIsInCorrectSequence(string $sequence, string $dstId, int $flow): void
    {
        $preReviewer = $this->reviewerRepo->fetchPreReviewer($dstId, $sequence);
        $latestReview = $this->reviewRepo->fetchLastestReview($dstId);

        if ($preReviewer) {
            $preEmployeeId = $preReviewer->employeeId;

            if (!$latestReview || $latestReview->employeeId != $preEmployeeId || $latestReview->result != 'approved' || $latestReview->flow != $flow) {
                throw new \Exception('you havent authorized to review yet');
            }
        }
    }
}

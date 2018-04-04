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

class VerifyReviewAdapter
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

    public function verify(string $employeeId, string $dstId, int $flow)
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

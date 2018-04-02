<?php
namespace Wec\Review\Repo;

use Gap\Dto\DateTime;
use Wec\Review\Dto\ReviewDto;
use Gap\Db\MySql\Collection;
use Gap\Db\MySql\SqlBuilder\SelectSqlBuilder;

class ReviewRepo extends RepoBase
{
    public function approve(string $employeeId, string $dstId, string $message, int $flow): void
    {
        $this->createReviewRecord($employeeId, $dstId, $message, 'approved', $flow);
    }

    public function reject(string $employeeId, string $dstId, string $message, int $flow): void
    {
        $this->createReviewRecord($employeeId, $dstId, $message, 'rejected', $flow);
    }

    protected function getTable(): string
    {
        return lcfirst(str_replace('_', '', ucwords($this->dst, '_'))) . '_review';
    }

    protected function createReviewRecord(string $employeeId, string $dstId, string $message, string $result, int $flow): void
    {
        $created = new DateTime();

        $this->cnn->isb()
            ->insert($this->getTable())
            ->field(
                'reviewId',
                $this->getDstKey(),
                'employeeId',
                'message',
                'result',
                'flow',
                'created'
            )
            ->value()
                ->addStr($this->cnn->zid())
                ->addStr($dstId)
                ->addStr($employeeId)
                ->addStr($message)
                ->addStr($result)
                ->addInt($flow)
                ->addDateTime($created)
            ->end()
            ->execute();
    }

    public function fetchReviewByReviewId(string $reviewId): ? ReviewDto
    {
        if (!$reviewId) {
            throw \Exception('reviewId cannot be null');
        }

        $ssb = $this->getBasicReviewSsb();

        return $ssb
            ->where()
                ->expect('reviewId')->equal()->str($reviewId)
            ->end()
            ->fetch(ReviewDto::class);
    }

    public function fetchPreReview(string $dstId, string $employeeId, int $flow): ? ReviewDto
    {
        if (!$reviewId) {
            throw \Exception('reviewId cannot be null');
        }
        
        if (!$employeeId) {
            throw \Exception('employeeId cannot be null');
        }

        if (!$flow) {
            throw \Exception('flow cannot be null');
        }

        $ssb = $this->getBasicReviewSsb();

        return $ssb
            ->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
                ->andExpect('employeeId')->equal()->str($employeeId)
                ->andExpect('flow')->equal()->int($flow)
            ->end()
            ->fetch(ReviewDto::class);
    }

    public function listReview(string $dstId): Collection
    {
        if (!$dstId) {
            throw \Exception('dstId cannot be null');
        }

        $ssb = $this->getBasicReviewSsb();

        return $ssb->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
            ->end()
            ->ascOrderBy('created')
            ->list(ReviewDto::class);
    }

    public function fetchLastestReview(string $dstId): ? ReviewDto
    {
        if (!$dstId) {
            throw \Exception('dstId cannot be null');
        }

        $ssb = $this->getBasicReviewSsb();

        return $ssb
            ->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
            ->end()
            ->descOrderBy('t.created')
            ->limit(1)
            ->fetch(ReviewDto::class);
    }

    public function fetchReviewByEmployeeIdInFlow(string $dstId, string $employeeId, int $flow): ? ReviewDto
    {
        $ssb = $this->getBasicReviewSsb();
        
        return $ssb
            ->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
                ->andExpect('employeeId')->equal()->str($employeeId)
                ->andExpect('flow')->equal()->int($flow)
            ->end()
            ->limit(1)
            ->fetch(ReviewDto::class);
    }

    public function getBasicReviewSsb(): SelectSqlBuilder
    {
        $table = $this->getTable();
        return $this->cnn
            ->ssb()
            ->select(
                't.reviewId',
                't.employeeId',
                't.result',
                't.message',
                't.flow',
                't.created'
            )
            ->from("$table t")
            ->end();
    }
}

<?php
namespace Wec\Review\Repo;

use Gap\Dto\DateTime;

class ReviewRepo extends RepoBase
{
    public function approve(string $employeeId, string $dstId, string $message = ''): void
    {
        $this->createReviewRecord($employeeId, $dstId, $message, 'approved');
    }

    public function reject(string $employeeId, string $dstId, string $message = ''): void
    {
        $this->createReviewRecord($employeeId, $dstId, $message, 'rejected');
    }

    protected function getTable(): string
    {
        return lcfirst(str_replace('_', '', ucwords($this->dst, '_'))) . '_review';
    }

    protected function createReviewRecord(string $employeeId, string $dstId, string $message, string $result): void
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
                'created'
            )
            ->value()
                ->addStr($this->cnn->zid())
                ->addStr($dstId)
                ->addStr($employeeId)
                ->addStr($message)
                ->addStr($result)
                ->addDateTime($created)
            ->end()
            ->execute();
    }
}

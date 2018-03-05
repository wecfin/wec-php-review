<?php
namespace Wec\Review\Repo;

class ReviewRepo extends RepoBase
{
    public function approve(string $employeeId, string $dstId, string $massage = ''): void
    {
        $created = date(\DateTime::ATOM);

        $this->cnn->insert($this->getTable())
            ->value('reviewId', '1-' . uniqid())
            ->value($this->getDstKey(), $dstId)
            ->value('employeeId', $employeeId)
            ->value('message', $massage)
            ->value('result', 'approved')
            ->value('created', $created)
            ->execute();
    }

    public function reject(string $employeeId, string $dstId, string $massage = ''): void
    {
        $created = date(\DateTime::ATOM);

        $this->cnn->insert($this->getTable())
            ->value('reviewId', '1-' . uniqid())
            ->value($this->getDstKey(), $dstId)
            ->value('employeeId', $employeeId)
            ->value('message', $massage)
            ->value('result', 'rejected')
            ->value('created', $created)
            ->execute();
    }

    protected function getTable(): string
    {
        return lcfirst(str_replace('_', '', ucwords($this->dst, '_'))) . '_review';
    }
}

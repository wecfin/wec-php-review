<?php
namespace Wec\Review\Repo;

use Wec\Review\Dto\ReviewerDto;

class ReviewerRepo extends RepoBase
{
    public function addReviewer(string $dstId, ReviewerDto $reviewer): void
    {
        if (empty($reviewer->created)) {
            $reviewer->created = date(\DateTime::ATOM); // todo need test
        }

        $this->cnn->insert($this->getTable())
            ->value($this->getDstKey(), $dstId)
            ->value('employeeId', $reviewer->employeeId)
            ->value('fullName', $reviewer->fullName)
            ->value('sequence', $reviewer->sequence)
            ->value('created', $reviewer->created)
            ->execute();
    }

    protected function getTable(): string
    {
        return $this->dst . '_reviewer';
    }
}

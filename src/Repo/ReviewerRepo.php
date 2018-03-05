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

    public function listReviewer(string $dstId): DataSet
    {
        if (!$dstId) {
            throw new \Exception('dstId cannot be null');
        }

        $ssb = $this->cnn->select()
            ->from($this->getTable())
            ->where($this->getDstKey(), '=', $dstId);

        return $this->dataSet($ssb, ReviewerDto::class);
    }

    public function fetchReviewer($dstId, $employeeId)
    {
        if (!$dstId) {
            throw \Exception('desId cannot be null');
        }

        if (!$employeeId) {
            throw \Exception('employeeId cannot be null');
        }

        return $this->cnn->select()
            ->from($this->getTable())
            ->where($this->getDstKey(), '=', $dstId)
            ->andWhere('employeeId', '=', $employeeId)
            ->fetch(ReviewerDto::class);
    }

    protected function getTable(): string
    {
        return $this->dst . '_reviewer';
    }
}

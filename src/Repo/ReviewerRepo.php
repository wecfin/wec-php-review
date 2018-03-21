<?php
namespace Wec\Review\Repo;

use Wec\Review\Dto\ReviewerDto;
use Gap\Dto\DateTime;
use Gap\Db\MySql\Collection;

class ReviewerRepo extends RepoBase
{
    public function addReviewer(string $dstId, ReviewerDto $reviewer): void
    {
        $reviewer->created = new DateTime();

        $this->cnn->isb()
            ->insert($this->getTable())
            ->field(
                $this->getDstKey(),
                'employeeId',
                'fullName',
                'sequence',
                'created'
            )
            ->value()
                ->addStr($dstId)
                ->addStr($reviewer->employeeId)
                ->addStr($reviewer->fullName)
                ->addStr($reviewer->sequence)
                ->addDateTime($reviewer->created)
            ->end()
            ->execute();
    }

    public function listReviewer(string $dstId): Collection
    {
        if (!$dstId) {
            throw new \Exception('dstId cannot be null');
        }

        return $this->cnn->ssb()
            ->select('*')
            ->from($this->getTable())
            ->end()
            ->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
            ->end()
            ->ascOrderBy('sequence')
            ->list(ReviewerDto::class);
    }

    public function fetchReviewer($dstId, $employeeId): ? ReviewerDto
    {
        if (!$dstId) {
            throw \Exception('dstId cannot be null');
        }

        if (!$employeeId) {
            throw \Exception('employeeId cannot be null');
        }

        return $this->cnn->ssb()
            ->select('*')
            ->from($this->getTable())
            ->end()
            ->where()
                ->expect($this->getDstKey())->equal()->str($dstId)
                ->andExpect('employeeId')->equal()->str($employeeId)
            ->end()
            ->fetch(ReviewerDto::class);
    }

    protected function getTable(): string
    {
        return $this->dst . '_reviewer';
    }
}

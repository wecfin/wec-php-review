<?php
namespace Wec\Review\Repo;

use Gap\Dto\DateTime;
use Wec\Review\Dto\ReviewFlowDto;
use Gap\Db\MySql\Collection;

class ReviewFlowRepo extends RepoBase
{
    public function fetchCurrentReviewFlow(string $dstId): int
    {
        if (!$dstId) {
            throw new \Exception('dstId cannot be null');
        }

        $this->initParamIndex();
        $reviewFlow = $this->cnn->ssb()
            ->select('*')
            ->from($this->getTable())
            ->end()
            ->where()
                ->expect('dstType')->equal()->str($this->getDst())
                ->andExpect('dstId')->equal()->str($dstId)
            ->end()
            ->descOrderBy('created')
            ->limit(1)
            ->fetch(ReviewFlowDto::class);

        if ($reviewFlow) {
            return $reviewFlow->flow;
        }

        $flow = 1;
        $this->createReviewFlow($dstId, $flow);
        return $flow;
    }


    public function createReviewFlow(string $dstId, int $flow): void
    {
        $created = new DateTime();
        $this->initParamIndex();
        $this->cnn->isb()
            ->insert($this->getTable())
            ->field(
                'reviewFlowId',
                'dstType',
                'dstId',
                'flow',
                'created'
            )
            ->value()
                ->addStr($this->cnn->zid())
                ->addStr($this->getDst())
                ->addStr($dstId)
                ->addInt($flow)
                ->addDateTime($created)
            ->end()
            ->execute();
    }

    protected function getTable(): string
    {
        return 'review_flow';
    }
}

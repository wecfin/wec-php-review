<?php
namespace Wec\Review;

use Gap\Database\DatabaseManager;
use Wec\Review\Repo\ReviewRepo;
use Wec\Review\Repo\ReviewerRepo;

class ReviewAdapter
{
    protected $dst;
    protected $dmg;
    protected $reviewRepo;
    protected $reviewerRepo;

    public function __construct(string $dst, DatabaseManager $dmg)
    {
        $this->dst = $dst;
        $this->dmg = $dmg;

        $this->reviewRepo = new ReviewRepo($this->dmg);
        $this->reviewerRepo = new ReviewerRepo($this->dmg);

        $this->reviewRepo->setDst($this->dst);
        $this->reviewerRepo->setDst($this->dst);
    }
}

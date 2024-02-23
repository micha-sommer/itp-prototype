<?php

namespace App\Service;

use App\Repository\ContestantRepository;

class ContestantService
{
    private int $limit;
    private ContestantRepository $contestantRepository;

    public function __construct(int $limit, ContestantRepository $contestantRepository)
    {
        $this->limit = $limit;
        $this->contestantRepository = $contestantRepository;
    }

    public function getCurrentLimit(): int
    {
        return $this->limit - $this->contestantRepository->getContestantCount();
    }
}

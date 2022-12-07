<?php

namespace App\Service;

use App\Repository\ContestantRepository;
use App\Repository\OfficialRepository;

class AccommodationService
{
    private int $limit;
    private OfficialRepository $officialRepository;
    private ContestantRepository $contestantRepository;

    public function __construct(int $limit, OfficialRepository $officialRepository, ContestantRepository $contestantRepository)
    {
        $this->limit = $limit;
        $this->officialRepository = $officialRepository;
        $this->contestantRepository = $contestantRepository;
    }

    public function getCurrentLimit(): int
    {
        return $this->limit
            - $this->contestantRepository->getAccommodationCount()
            - $this->officialRepository->getAccommodationCount();
    }
}

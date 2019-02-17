<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Entity\Transport;
use App\Enum\AgeCategoryEnum;
use App\Enum\WeightCategoryEnum;
use App\Repository\ContestantsRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     * @param RegistrationsRepository $registrationsRepository
     * @param ContestantsRepository $contestantsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcome(RegistrationsRepository $registrationsRepository, ContestantsRepository $contestantsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $registration = $this->getUser();

        $arrival = null;
        $departure = null;

        $filterFunction = function (Registration $registration){
            return \count($registration->getContestants())>0;
        };

        $registrationCount = \count(\array_filter($registrationsRepository->findAll(), $filterFunction));

        foreach (AgeCategoryEnum::asArray() as $age) {
            foreach (WeightCategoryEnum::asArray() as $weight) {
                $categories[$age][$weight] = $contestantsRepository->count(['ageCategory' => $age, 'weightCategory' => $weight]);
            }
        }

        $categories[AgeCategoryEnum::cadet]['total'] = $contestantsRepository->count(['ageCategory' => AgeCategoryEnum::cadet]);
        $categories[AgeCategoryEnum::cadet]['camp'] = $contestantsRepository->_count(Criteria::create()->where(Criteria::expr()->eq('ageCategory', AgeCategoryEnum::cadet))->andWhere(Criteria::expr()->neq('itc','no')));
        $categories[AgeCategoryEnum::junior]['total'] = $contestantsRepository->count(['ageCategory' => AgeCategoryEnum::junior]);
        $categories[AgeCategoryEnum::junior]['camp'] = $contestantsRepository->_count(Criteria::create()->where(Criteria::expr()->eq('ageCategory', AgeCategoryEnum::junior))->andWhere(Criteria::expr()->neq('itc','no')));
        $categories['total'] = $contestantsRepository->count([]);
        $categories['camp'] = $contestantsRepository->count([]);

        if ($registration) {
            $arrival = $registration->getTransports()->filter(function (Transport $transport) {
                return $transport->getIsArrival();
            })->first();
            $departure = $registration->getTransports()->filter(function (Transport $transport) {
                return !$transport->getIsArrival();
            })->first();
        }

        return $this->render('welcome/index.html.twig', [
            'registration' => $registration,
            'arrival' => $arrival,
            'departure' => $departure,
            'clubsCount' => $registrationCount,
            'categories' => $categories,
        ]);
    }
}

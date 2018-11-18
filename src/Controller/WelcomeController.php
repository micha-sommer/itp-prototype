<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Enum\AgeCategoryEnum;
use App\Enum\WeightCategoryEnum;
use App\Repository\ContestantsRepository;
use App\Repository\RegistrationsRepository;
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

        $registrationCount = \count($registrationsRepository->findAll());

        foreach (AgeCategoryEnum::asArray() as $age) {
            foreach (WeightCategoryEnum::asArray() as $weight) {
                $categories[$age][$weight] = $contestantsRepository->countCategory($age, $weight);
            }
        }

        $categories[AgeCategoryEnum::cadet]['total'] = $contestantsRepository->countCategory(AgeCategoryEnum::cadet);
        $categories[AgeCategoryEnum::cadet]['camp'] = $contestantsRepository->countCamp(AgeCategoryEnum::cadet);
        $categories[AgeCategoryEnum::junior]['total'] = $contestantsRepository->countCategory(AgeCategoryEnum::junior);
        $categories[AgeCategoryEnum::junior]['camp'] = $contestantsRepository->countCamp(AgeCategoryEnum::junior);
        $categories['total'] = $contestantsRepository->countCategory();
        $categories['camp'] = $contestantsRepository->countCamp();

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

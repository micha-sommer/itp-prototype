<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Entity\Transport;
use App\Enum\AgeCategoryEnum;
use App\Enum\WeightCategoryEnum;
use App\Repository\ContestantsRepository;
use App\Repository\RegistrationsRepository;
use App\Services\Globals;
use function array_filter;
use function count;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    private Globals $globals;

    public function __construct(Globals $globals)
    {
        $this->globals = $globals;
    }

    /**
     * @Route("/", name="welcome")
     */
    public function welcome(RegistrationsRepository $registrationsRepository, ContestantsRepository $contestantsRepository): Response
    {
        if ($this->globals->isActive()) {
            return $this->activeWelcome($registrationsRepository, $contestantsRepository);
        }

        return $this->inactiveWelcome();
    }

    /**
     * @Route(
     *     "/{age}/{weight}",
     *     name="overview",
     *     requirements={
     *          "age" = "cadet|junior",
     *          "weight" = "-40|-44|-48|-52|-57|-63|-70|\+70|-78|\+78"
     *     }
     * )
     */
    public function showCategory(ContestantsRepository $contestantsRepository, $age, $weight): Response
    {
        if (('cadet' == $age && '-78' == $weight)
            || ('cadet' == $age && '+78' == $weight)
            || ('junior' == $age && '-40' == $weight)
            || ('junior' == $age && '+70' == $weight)
        ) {
            throw $this->createNotFoundException('Wrong category');
        }

        $contestants = $contestantsRepository->findBy(['ageCategory' => $age, 'weightCategory' => $weight]);

        return $this->render('welcome/contestants.html.twig', [
            'age' => $age,
            'weight' => $weight,
            'contestants' => $contestants,
        ]);
    }

    private function activeWelcome(RegistrationsRepository $registrationsRepository, ContestantsRepository $contestantsRepository): Response
    {
        $registration = $this->getUser();

        $arrival = null;
        $departure = null;

        $filterFunction = function (Registration $registration) {
            return count($registration->getContestants()) > 0;
        };

        $registrationCount = count(array_filter($registrationsRepository->findAll(), $filterFunction));

        foreach (AgeCategoryEnum::asArray() as $age) {
            foreach (WeightCategoryEnum::asArray() as $weight) {
                $categories[$age][$weight] = $contestantsRepository->count(['ageCategory' => $age, 'weightCategory' => $weight]);
            }
        }
        $distinctCountries = $registrationsRepository->findDistinctCountries();

        $categories[AgeCategoryEnum::cadet]['total'] = $contestantsRepository->count(['ageCategory' => AgeCategoryEnum::cadet]);
        $categories[AgeCategoryEnum::cadet]['camp'] = $contestantsRepository->_count(Criteria::create()->where(Criteria::expr()->eq('ageCategory', AgeCategoryEnum::cadet))->andWhere(Criteria::expr()->neq('itc', 'no')));
        $categories[AgeCategoryEnum::junior]['total'] = $contestantsRepository->count(['ageCategory' => AgeCategoryEnum::junior]);
        $categories[AgeCategoryEnum::junior]['camp'] = $contestantsRepository->_count(Criteria::create()->where(Criteria::expr()->eq('ageCategory', AgeCategoryEnum::junior))->andWhere(Criteria::expr()->neq('itc', 'no')));
        $categories['total'] = $contestantsRepository->count([]);
        $categories['camp'] = $contestantsRepository->count([]);

        if ($registration) {
            $arrival = $registration->getTransports()->filter(static function (Transport $transport) {
                return $transport->getIsArrival();
            })->first();
            $departure = $registration->getTransports()->filter(static function (Transport $transport) {
                return !$transport->getIsArrival();
            })->first();
        }

        return $this->render('welcome/index.html.twig', [
            'registration' => $registration,
            'arrival' => $arrival,
            'departure' => $departure,
            'clubsCount' => $registrationCount,
            'categories' => $categories,
            'countries' => $distinctCountries,
        ]);
    }

    private function inactiveWelcome(): Response
    {
        return $this->render('welcome/waiting.html.twig');
    }
}

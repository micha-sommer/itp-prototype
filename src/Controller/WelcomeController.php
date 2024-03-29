<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Repository\ContestantRepository;
use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    private ContestantRepository $contestantRepository;
    private RegistrationRepository $registrationRepository;

    public function __construct(
        ContestantRepository   $contestantRepository,
        RegistrationRepository $registrationRepository,
    )
    {
        $this->contestantRepository = $contestantRepository;
        $this->registrationRepository = $registrationRepository;
    }

    #[Route('/')]
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('welcome', ['_locale' => 'de']);
    }

    #[Route('/{_locale<%app.supported_locales%>}')]
    public function index(): Response
    {
        return $this->redirectToRoute('welcome');
    }

    #[Route('/{_locale<%app.supported_locales%>}/welcome', name: 'welcome')]
    public function welcome(): Response
    {
        if ($this->getParameter('app.is_active')) {
            return $this->activeWelcome();

        } else {
            return $this->render('welcome/waiting.html.twig');
        }
    }

    /**
     * @return Response
     */
    public function activeWelcome(): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->activeAuthenticatedWelcome();
        } else {
            return $this->activeAnonymousWelcome();
        }
    }

    /**
     * @return Response
     */
    public function activeAuthenticatedWelcome(): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        return $this->render('welcome/index.html.twig', [
            'registration' => $registration,
            'contestants' => $registration->getContestants(),
            'officials' => $registration->getOfficials(),
        ]);
    }

    /**
     * @return Response
     */
    public function activeAnonymousWelcome(): Response
    {
        $distinctCountries = $this->registrationRepository->findDistinctCountries();

        $registrationCount = count(
            array_filter(
                $this->registrationRepository->findAll(),
                fn(Registration $registration) => count($registration->getContestants()) > 0
            ));


        $categories['total'] = $this->contestantRepository->countByCriteria(
            Criteria::create()
                ->where(Criteria::expr()->neq('weightCategory', 'camp_only'))
        );

        $categories['cadet']['total'] = $this->contestantRepository->countByCriteria(
            Criteria::create()
                ->where(Criteria::expr()->eq('ageCategory', 'cadet'))
                ->andWhere(Criteria::expr()->neq('weightCategory', 'camp_only'))
        );
        $categories['junior']['total'] = $this->contestantRepository->countByCriteria(
            Criteria::create()
                ->where(Criteria::expr()->eq('ageCategory', 'junior'))
                ->andWhere(Criteria::expr()->neq('weightCategory', 'camp_only'))
        );

        foreach (['cadet', 'junior'] as $age) {
            foreach (['-40', '-44', '-48', '-52', '-57', '-63', '-70', '+70', '-78', '+78'] as $weight) {
                $categories[$age][$weight] =
                    $this->contestantRepository->count(['ageCategory' => $age, 'weightCategory' => $weight]);
            }
        }

        return $this->render('welcome/anonymous.html.twig', [
            'countries' => $distinctCountries,
            'clubsCount' => $registrationCount,
            'categories' => $categories,
        ]);
    }
}

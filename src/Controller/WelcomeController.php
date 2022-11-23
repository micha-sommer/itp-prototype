<?php

namespace App\Controller;

use App\Repository\ContestantRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    private ContestantRepository $contestantRepository;
    private RegistrationRepository $registrationRepository;

    public function __construct(
        ContestantRepository $contestantRepository,
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
        return $this->render('welcome/index.html.twig');
    }

    /**
     * @return Response
     */
    public function activeAnonymousWelcome(): Response
    {
        $distinctCountries = $this->registrationRepository->findDistinctCountries();

        return $this->render('welcome/anonymous.html.twig', [
            'countries' => $distinctCountries,
        ]);
    }
}

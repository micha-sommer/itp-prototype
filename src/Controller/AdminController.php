<?php

namespace App\Controller;

use App\Repository\ContestantRepository;
use App\Repository\OfficialRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_index')]
    public function index(
        RegistrationRepository $registrationRepository,
        OfficialRepository     $officialRepository,
        ContestantRepository   $contestantRepository,
    ): Response
    {
        $registrations = $registrationRepository->findAll();

        $officialsCount = $officialRepository->count([]);
        $contestantsCount = $contestantRepository->count([]);

        $packageACount = $officialRepository->count(['itcSelection' => 'pack-A'])
            + $contestantRepository->count(['itcSelection' => 'pack-A']);
        $packageBCount = $officialRepository->count(['itcSelection' => 'pack-B'])
            + $contestantRepository->count(['itcSelection' => 'pack-B']);
        $packageCCount = $officialRepository->count(['itcSelection' => 'pack-C'])
            + $contestantRepository->count(['itcSelection' => 'pack-C']);
        $oneDayCount = $officialRepository->count(['itcSelection' => '1-day'])
            + $contestantRepository->count(['itcSelection' => '1-day']);
        $twoDaysCount = $officialRepository->count(['itcSelection' => '2-day'])
            + $contestantRepository->count(['itcSelection' => '2-day']);
        $threeDaysCount = $officialRepository->count(['itcSelection' => '3-day'])
            + $contestantRepository->count(['itcSelection' => '3-day']);


        return $this->render('admin/index.html.twig', [
            'registrations' => $registrations,
            'registrationsCount' => $registrationRepository->count([]),
            'officialsCount' => $officialsCount,
            'contestantsCount' => $contestantsCount,
            'packageACount' => $packageACount,
            'packageBCount' => $packageBCount,
            'packageCCount' => $packageCCount,
            'oneDayCount' => $oneDayCount,
            'twoDaysCount' => $twoDaysCount,
            'threeDaysCount' => $threeDaysCount,
        ]);
    }
}

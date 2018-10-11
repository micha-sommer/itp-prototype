<?php

namespace App\Controller;

use App\Entity\OfficialRepository;
use App\Entity\Registration;
use App\Entity\Transport;
use App\Repository\ContestantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     */
    public function welcome(): \Symfony\Component\HttpFoundation\Response
    {
        $registration = $this->getUser();

        $officials = null;
        $contestants = null;
        $arrival = null;
        $departure = null;

        if ($registration) {
            $officials = $registration->getOfficials();
            $contestants = $registration->getContestants();
            $arrival = $registration->getTransports()->filter(function (Transport $transport) {
                    return $transport->getIsArrival();
                })->first();
            $departure = $registration->getTransports()->filter(function (Transport $transport) {
                    return !$transport->getIsArrival();
                })->first();
        }

        return $this->render('welcome/index.html.twig', [
            'officials' => $officials,
            'contestants' => $contestants,
            'arrival' => $arrival,
            'departure' => $departure,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Transport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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

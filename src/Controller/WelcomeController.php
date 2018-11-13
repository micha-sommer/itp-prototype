<?php

namespace App\Controller;

use App\Entity\Registration;
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

        $arrival = null;
        $departure = null;

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
        ]);
    }
}

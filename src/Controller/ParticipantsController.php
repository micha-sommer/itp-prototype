<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParticipantsController extends Controller
{
    /**
     * @Route("/participants", name="participants")
     */
    public function index()
    {
        return $this->render('participants/participants.html.twig', [
            'controller_name' => 'ParticipantsController',
        ]);
    }
}

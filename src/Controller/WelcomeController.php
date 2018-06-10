<?php

namespace App\Controller;

use App\Entity\Registration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     */
    public function welcome()
    {
        return $this->render('welcome/index.html.twig', [
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Contestant;
use App\Entity\Registration;
use App\Form\ContestantsListType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}')]
class ParticipantsController extends AbstractController
{
    #[Route('/contestants', name: 'contestants')]
    public function contestants(Request $request, ManagerRegistry $doctrine): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        if ($registration->getContestants()->isEmpty()) {
            $registration->addContestant(new Contestant());
        }

        $form = $this->createForm(
            ContestantsListType::class,
            $registration,
            ['year' => $this->getParameter('app.year')],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
        }

        return $this->render('participants/contestants.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

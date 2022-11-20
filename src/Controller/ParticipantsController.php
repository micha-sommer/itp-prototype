<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Form\ContestantsListType;
use App\Form\OfficialsListType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}')]
class ParticipantsController extends AbstractController
{
    #[Route('/contestants', name: 'contestants')]
    public function contestants(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(
            ContestantsListType::class,
            $registration,
            ['year' => $this->getParameter('app.year')],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('participants/contestants.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/officials', name: 'officials')]
    public function officials(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(
            OfficialsListType::class,
            $registration,
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('participants/officials.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

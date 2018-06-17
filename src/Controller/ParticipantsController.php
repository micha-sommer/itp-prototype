<?php

namespace App\Controller;

use App\Entity\Contestant;
use App\Entity\ContestantsList;
use App\Entity\Official;
use App\Entity\OfficialsList;
use App\Form\ContestantsListType;
use App\Form\OfficialsListType;
use App\Repository\ContestantsRepository;
use App\Repository\OfficialsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParticipantsController extends Controller
{
    /**
     * @Route("/officials", name="officials")
     * @param Request $request
     * @param OfficialsRepository $officialsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function officials(Request $request, OfficialsRepository $officialsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $officialsBefore = $officialsRepository->findBy(['registration' => $this->getUser()]);

        $officialsAfter = new OfficialsList();
        $officialsAfter->setList(new ArrayCollection($officialsBefore));

        if ($officialsAfter->getList()->isEmpty()) {
            $officialsAfter->addOfficial(new Official());
        }

        $form = $this->createForm(OfficialsListType::class, $officialsAfter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // check for deleted officials
            foreach ($officialsBefore as $official) {
                if (false === $officialsAfter->getList()->contains($official)) {
                    $em->remove($official);
                }
            }

            // check for added officials
            foreach ($officialsAfter->getList() as $official) {
                if (false === \in_array($official, $officialsBefore, true)) {
                    $official->setRegistration($this->getUser());
                    $em->persist($official);
                }
            }
            $em->flush();
        }

        return $this->render('participants/officials.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contestants", name="contestants")
     * @param Request $request
     * @param ContestantsRepository $contestantsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contestants(Request $request, ContestantsRepository $contestantsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $contestantsBefore = $contestantsRepository->findBy(['registration' => $this->getUser()]);

        $contestantsAfter = new ContestantsList();
        $contestantsAfter->setList(new ArrayCollection($contestantsBefore));

        if ($contestantsAfter->getList()->isEmpty()) {
            $contestantsAfter->addContestant(new Contestant());
        }

        $form = $this->createForm(ContestantsListType::class, $contestantsAfter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            // check for deleted contestants
            foreach ($contestantsBefore as $contestant) {
                if (false === $contestantsAfter->getList()->contains($contestant)) {
                    $em->remove($contestant);
                }
            }

            // check for added contestants
            foreach ($contestantsAfter->getList() as $contestant) {
                if (false === \in_array($contestant, $contestantsBefore, true)) {
                    $contestant->setRegistration($this->getUser());
                    $em->persist($contestant);
                }
            }
            $em->flush();
        }

        return $this->render('participants/contestants.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

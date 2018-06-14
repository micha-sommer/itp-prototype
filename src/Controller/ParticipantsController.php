<?php

namespace App\Controller;

use App\Entity\Official;
use App\Entity\OfficialsList;
use App\Form\OfficialsListType;
use App\Repository\OfficialsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParticipantsController extends Controller
{
    /**
     * @Route("/all_officials", name="officials_listedit")
     * @param Request $request
     * @param OfficialsRepository $officialsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listEditOfficials(Request $request, OfficialsRepository $officialsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $officialsDavor = $officialsRepository->findBy(['registration' => $this->getUser()]);

        $officialsAfter = new OfficialsList();
        $officialsAfter->setList(new ArrayCollection($officialsDavor));

        $form = $this->createForm(OfficialsListType::class, $officialsAfter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            dump('valid');

            // check for deleted officials
            foreach ($officialsDavor as $official) {
                if (false === $officialsAfter->getList()->contains($official)) {
                    dump('remove:'.$official->getId());
                    $em->remove($official);
                }
            }

            // check for added officials
            foreach ($officialsAfter->getList() as $official) {
                if (false === \in_array($official, $officialsDavor, true)) {
                    dump('add:'.$official->getId());
                    $official->setRegistration($this->getUser());
                    $em->persist($official);
                }
            }
            $em->flush();
            $this->redirectToRoute('edit_competitors');
        }
        else {
            dump('not valid');
        }


        return $this->render('participants/official_listedit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/officials", name="officials_index")
     * @param OfficialsRepository $officialsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listOfficials(OfficialsRepository $officialsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $officials = $officialsRepository->findBy(['registration' => $this->getUser()]);

        dump($officials);

        return $this->render('participants/officials_index.html.twig', [
            'officials' => $officials,
        ]);
    }

    /**
     * @Route("/official/{id}", requirements={"id": "\d+"}, methods={"GET"}, name="official_edit")
     * @param Official $official
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editOfficial(Official $official)
    {
        return $this->render('participants/official_edit.html.twig');
    }

    /**
     * @Route("/officials", name="edit_competitors")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCompetitors(Request $request): \Symfony\Component\HttpFoundation\Response
    {

    }
}

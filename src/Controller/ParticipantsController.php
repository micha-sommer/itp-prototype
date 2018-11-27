<?php

namespace App\Controller;

use App\Entity\Contestant;
use App\Entity\ContestantsList;
use App\Entity\Official;
use App\Entity\OfficialsList;
use App\Entity\Transport;
use App\Form\ContestantsListType;
use App\Form\OfficialsListType;
use App\Form\TransportType;
use App\Repository\ContestantsRepository;
use App\Repository\OfficialsRepository;
use App\Repository\TransportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ParticipantsController extends Controller
{
    /**
     * @Route("/officials", name="officials")
     * @param Request $request
     * @param OfficialsRepository $officialsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function officials(Request $request, OfficialsRepository $officialsRepository): Response
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
                    $official->setTimestamp(new \DateTime());
                    $em->persist($official);
                    $em->flush();
                }
            }
            $em->flush();
            if ($request->request->get('back')) {
                return $this->redirectToRoute('welcome');
            }
        }

        return $this->render('participants/officials.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contestants", name="contestants")
     * @param Request $request
     * @param ContestantsRepository $contestantsRepository
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function contestants(Request $request, ContestantsRepository $contestantsRepository, ValidatorInterface $validator, TranslatorInterface $translator): Response
    {
        $contestantsBefore = $contestantsRepository->findBy(['registration' => $this->getUser()]);

        $contestantsAfter = new ContestantsList();
        $contestantsAfter->setList(new ArrayCollection($contestantsBefore));

        if ($contestantsAfter->getList()->isEmpty()) {
            $contestantsAfter->addContestant(new Contestant());
        }

        $form = $this->createForm(ContestantsListType::class, $contestantsAfter);

        $has_errors = false;

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

                $errors = $validator->validate($contestant);
                if (\count($errors) > 0) {
                    $em->detach($contestant);
                    $has_errors = true;
                    $forms = $form->get('list');
                    foreach ($errors as $error) {
                        if ($error->getPropertyPath() === 'validYearAgeCombination') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$contestantsAfter->getList()->indexOf($contestant)]->get('year')->addError($formErrorInvalidAge);
                        } elseif ($error->getPropertyPath() === 'validAgeWeightCombination') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$contestantsAfter->getList()->indexOf($contestant)]->get('weight_category')->addError($formErrorInvalidAge);
                        } elseif ($error->getPropertyPath() === 'validCamp') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$contestantsAfter->getList()->indexOf($contestant)]->get('itc')->addError($formErrorInvalidAge);
                        }
                    }
                } else
                    if (false === \in_array($contestant, $contestantsBefore, true)) {
                        $contestant->setRegistration($this->getUser());
                        $contestant->setTimestamp(new \DateTime());
                        $em->persist($contestant);
                    }
            }
            $em->flush();
            if (!$has_errors && $request->request->get('back')) {
                return $this->redirectToRoute('welcome');
            }
        }

        return $this->render('participants/contestants.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/transports", name="transports")
     * @param Request $request
     * @param TransportRepository $transportRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function transports(Request $request, TransportRepository $transportRepository): Response
    {
        $arrival = $transportRepository->findOneBy(['registration' => $this->getUser(), 'isArrival' => true]);
        $departure = $transportRepository->findOneBy(['registration' => $this->getUser(), 'isArrival' => false]);

        $create_arrival = false;
        $create_departure = false;

        if ($arrival === null) {
            $arrival = new Transport();
            $arrival->setIsArrival(true);
            $arrival->setRegistration($this->getUser());
            $create_arrival = true;
        }
        if ($departure === null) {
            $departure = new Transport();
            $departure->setIsArrival(false);
            $departure->setRegistration($this->getUser());
            $create_departure = true;
        }


        $arrival_form = $this->get('form.factory')->createNamed('arrival', TransportType::class, $arrival);
        $departure_form = $this->get('form.factory')->createNamed('departure', TransportType::class, $departure);

        $arrival_form->handleRequest($request);
        $departure_form->handleRequest($request);

        $arrival_needed = $request->get('arrivalCheckbox') === 'on';
        $departure_needed = $request->get('departureCheckbox') === 'on';

        if ($departure_form->isSubmitted() && $departure_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($departure_needed) {
                if ($create_departure) {
                    $departure->setTimestamp(new \DateTime());
                    $entityManager->persist($departure);
                    $create_departure = false;
                }
            } else if (!$create_departure) {
                $entityManager->remove($departure);
                $create_departure = true;
            }
            $entityManager->flush();

            if ($request->request->get('back')) {
                return $this->redirectToRoute('welcome');
            }
        }
        if ($arrival_form->isSubmitted() && $arrival_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($arrival_needed) {
                if ($create_arrival) {
                    $arrival->setTimestamp(new \DateTime());
                    $entityManager->persist($arrival);
                    $create_arrival = false;
                }
            } else if (!$create_arrival) {
                $entityManager->remove($arrival);
                $create_arrival = true;
            }

            $entityManager->flush();

            if ($request->request->get('back')) {
                return $this->redirectToRoute('welcome');
            }
        }

        return $this->render('participants/transports.html.twig', [
            'arrival_checked' => !$create_arrival,
            'departure_checked' => !$create_departure,
            'arrival_form' => $arrival_form->createView(),
            'departure_form' => $departure_form->createView()
        ]);
    }
}

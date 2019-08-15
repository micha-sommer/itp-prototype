<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Entity\Transport;
use App\Form\ContestantsListType;
use App\Form\OfficialsListType;
use App\Form\TransportType;
use App\Repository\TransportRepository;
use DateTime;
use Exception as ExceptionAlias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ParticipantsController extends AbstractController
{
    /**
     * @Route("/officials", name="officials")
     * @param Request $request
     * @return Response
     * @throws ExceptionAlias
     */
    public function officials(Request $request): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(OfficialsListType::class, $registration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

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
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function contestants(Request $request, ValidatorInterface $validator, TranslatorInterface $translator): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(ContestantsListType::class, $registration);

        $has_errors = false;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // check for added contestants
            foreach ($registration->getContestants() as $contestant) {

                $errors = $validator->validate($contestant);
                if (\count($errors) > 0) {
                    $em->detach($contestant);
                    $has_errors = true;
                    $forms = $form->get('contestants');
                    $generalFormError = new FormError($translator->trans('errorMessageEntityNotSaved'));
                    $form->addError($generalFormError);
                    foreach ($errors as $error) {
                        if ($error->getPropertyPath() === 'validYearAgeCombination') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$registration->getContestants()->indexOf($contestant)]->get('year')->addError($formErrorInvalidAge);
                        } elseif ($error->getPropertyPath() === 'validAgeWeightCombination') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$registration->getContestants()->indexOf($contestant)]->get('weight_category')->addError($formErrorInvalidAge);
                        } elseif ($error->getPropertyPath() === 'validCamp') {
                            $formErrorInvalidAge = new FormError($translator->trans($error->getPropertyPath()));
                            $forms[$registration->getContestants()->indexOf($contestant)]->get('itc')->addError($formErrorInvalidAge);
                        }
                    }
                }
            }

            if (!$has_errors) {
                $em->flush();
                if ($request->request->get('back')) {
                    return $this->redirectToRoute('welcome');
                }
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
     * @return Response
     * @throws ExceptionAlias
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
                    $departure->setTimestamp(new DateTime());
                    $entityManager->persist($departure);
                    $create_departure = false;
                }
            } else if (!$create_departure) {
                $entityManager->remove($departure);
                $create_departure = true;
            }
            $entityManager->flush();
        }
        if ($arrival_form->isSubmitted() && $arrival_form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($arrival_needed) {
                if ($create_arrival) {
                    $arrival->setTimestamp(new DateTime());
                    $entityManager->persist($arrival);
                    $create_arrival = false;
                }
            } else if (!$create_arrival) {
                $entityManager->remove($arrival);
                $create_arrival = true;
            }
            $entityManager->flush();
        }

        if ($request->request->get('back')) {
            return $this->redirectToRoute('welcome');
        }

        return $this->render('participants/transports.html.twig', [
            'arrival_checked' => !$create_arrival,
            'departure_checked' => !$create_departure,
            'arrival_form' => $arrival_form->createView(),
            'departure_form' => $departure_form->createView()
        ]);
    }
}

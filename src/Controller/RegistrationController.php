<?php

namespace App\Controller;

use App\Form\ContactInformationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration/contactInformation", name="contactInformation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactInformation(Request $request)
    {
        $form = $this->createForm(ContactInformationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactInformation = $form->getData();
            dump($contactInformation);
            return $this->redirectToRoute('officials');
        }

        dump($form->isSubmitted());

        return $this->render('registration/contactInformation.html.twig', [
            'my_form' => $form->createView()
        ]);
    }
}

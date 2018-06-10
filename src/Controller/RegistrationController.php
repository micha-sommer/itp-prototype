<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $registration = new Registration();

        // 1) build the form
        $form = $this->createForm(RegistrationType::class, $registration);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($registration, $registration->getPlainPassword());
            $registration->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($registration);
            $entityManager->flush();

            $token = new UsernamePasswordToken(
                $registration,
                $password,
                'main',
                $registration->getRoles()
            );

            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $this->addFlash('success', 'You are now successfully registered!');

            return $this->redirectToRoute('welcome');
        }


        return $this->render('registration/registration.html.twig', [
            'my_form' => $form->createView()
        ]);
    }
}
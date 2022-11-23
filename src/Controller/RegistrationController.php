<?php

namespace App\Controller;

use App\Entity\Registration;
use App\Form\RegistrationEditFormType;
use App\Form\RegistrationEditPasswordFormType;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/{_locale<%app.supported_locales%>}/registration')]
class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/new', name: 'registration_new')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        TranslatorInterface         $translator,
    ): Response
    {
        $registration = new Registration();
        $form = $this->createForm(RegistrationFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $registration->setPassword(
                $userPasswordHasher->hashPassword(
                    $registration,
                    $form->get('plainPassword')->getData()
                )
            );

            $registration->setTimestamp(new DateTime());
            $entityManager->persist($registration);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $name = $translator->trans('registration.mail.name');
            $subject = $translator->trans('registration.mail.subject');
            $this->emailVerifier->sendEmailConfirmation(
                'registration_verify_email',
                $registration,
                (new TemplatedEmail())
                    ->from(new Address('anmeldung@thueringer-judoverband.de', $name))
                    ->to($registration->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/new.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/edit', name: 'registration_edit')]
    public function edit(
        Request                $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(RegistrationEditFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('registration/edit.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/edit/password', name: 'registration_edit_password')]
    public function changePassword(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
    ): Response
    {
        /** @var Registration $registration */
        $registration = $this->getUser();

        $form = $this->createForm(RegistrationEditPasswordFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $registration->setPassword(
                $userPasswordHasher->hashPassword(
                    $registration,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();
        }

        return $this->render('registration/edit_password.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'registration_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }
}

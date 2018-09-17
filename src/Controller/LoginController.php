<?php

namespace App\Controller;


use App\Form\LoginType;
use App\Form\ForgotPasswordType;
use App\Repository\RegistrationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $login_form = $this->createForm(LoginType::class);

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'my_form' => $login_form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/forgot_password", name="forgot_password")
     * @param Request $request
     * @param RegistrationsRepository $registrationsRepository
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPassword(Request $request, RegistrationsRepository $registrationsRepository, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $request->get('forgot_password')['email'];
            $registration = $registrationsRepository->findOneByEmail($email);

            if ($registration) {

                $hash = hash('sha256', $registration->getPassword(), false);
                $uid = $registration->getId();
                $root = 'http://localhost:8000';
                $link = $root . '/reset_password/' . $uid . '/' . $hash;

                $message = (new \Swift_Message('Forgot your password'))
                    ->setFrom('m.remmos@gmail.com')
                    ->setTo($registration->getEmail())
                    ->setBody($this->renderView('emails/forgot_password.html.twig', [
                        'registration' => $registration,
                        'link' => $link
                    ]), 'text/html');

                $mailer->send($message);
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('security/forgot_password.html.twig', [
            'my_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     * @param RegistrationsRepository $registrationsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin(RegistrationsRepository $registrationsRepository) : Response
    {
        $registrations = $registrationsRepository->findAll();
        return $this->render('security/admin.html.twig', [ 'registrations' => $registrations ]);
    }
}

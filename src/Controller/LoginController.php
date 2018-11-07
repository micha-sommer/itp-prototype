<?php

namespace App\Controller;


use App\Entity\ChangeSet;
use App\Entity\Contestant;
use App\Entity\Official;
use App\Entity\Registration;
use App\Entity\Transport;
use App\Form\ChangeSetType;
use App\Form\LoginType;
use App\Form\ForgotPasswordType;
use App\Repository\ChangeSetRepository;
use App\Repository\ContestantsRepository;
use App\Repository\OfficialsRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TransportRepository;
use Symfony\Component\Dotenv\Exception\FormatException;
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
    public function login(AuthenticationUtils $authenticationUtils, $locales, $defaultLocale): Response
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
                $root = 'https://www.anmeldung.thueringer-judoverband.de';
                $locale = $request->getLocale();

                $link = $root . '/' . $locale . '/reset_password/' . $uid . '/' . $hash;

                $message = (new \Swift_Message('Forgot your password'))
                    ->setFrom('anmeldung@thueringer-judoverband.de')
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
     * @param Request $request
     * @param RegistrationsRepository $registrationsRepository
     * @param OfficialsRepository $officialsRepository
     * @param ContestantsRepository $contestantsRepository
     * @param TransportRepository $transportRepository
     * @param ChangeSetRepository $changeSetRepository
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin(Request $request, RegistrationsRepository $registrationsRepository, OfficialsRepository $officialsRepository, ContestantsRepository $contestantsRepository, TransportRepository $transportRepository, ChangeSetRepository $changeSetRepository, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ChangeSetType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $after = $form->getData()['from_date'];
            $before = $form->getData()['to_date'];
            $newRegistrations = $registrationsRepository->findByDate($after, $before);
            $newOfficials = $officialsRepository->findByDate($after, $before);
            $newContestants = $contestantsRepository->findByDate($after, $before);
            $newTransports = $transportRepository->findByDate($after, $before);
            $changes = $changeSetRepository->findByDate($after, $before);

            $registrationsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newRegistrations) {
                return $changeSet->getName() === 'registration' && \in_array($changeSet->getNameId(), $newRegistrations, true);
            });

            $officialsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newOfficials) {
                return $changeSet->getName() === 'official' && \in_array($changeSet->getNameId(), $newOfficials, true);
            });
            $contestantsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newContestants) {
                return $changeSet->getName() === 'contestant' && \in_array($changeSet->getNameId(), $newContestants, true);
            });
            $transportChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newTransports) {
                return $changeSet->getName() === 'transport' && \in_array($changeSet->getNameId(), $newTransports, true);
            });

            $changes = \array_merge($registrationsChanges, $officialsChanges, $contestantsChanges, $transportChanges);

                usort($newOfficials, function (Official $a, Official $b) {
                    return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
                });

                usort($newContestants, function (Contestant $a, Contestant $b) {
                    return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
                });

            usort($newTransports, function (Transport $a, Transport $b) {
                return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
            });

            $message = (new \Swift_Message('Change history from ' . $after->format('Y-m-d H:i:s') . ' to ' . $before->format('Y-m-d H:i:s')))
                ->setFrom('anmeldung@thueringer-judoverband.de')
                ->setTo($this->getUser()->getEmail())
                ->setBody($this->renderView('emails/change_history.html.twig', [
                    'from_date' => $after->format('Y-m-d H:i:s'),
                    'to_date' => $after->format('Y-m-d H:i:s'),
                    'newRegistrations' => $newRegistrations,
                    'newOfficials' => $newOfficials,
                    'newContestants' => $newContestants,
                    'newTransports' => $newTransports,
                    'changes' => $changes,
                ]), 'text/html');

            $mailer->send($message);

            // unused code
            /*
            // remove new officials that are already part of new registrations
            $newOfficials = array_diff($newOfficials, array_map(function (Registration $o){
                return $o->getOfficials();
            }, $newRegistrations));

            // remove new contestnats that are already part of new registrations
            $newContestants = array_diff($newContestants, array_map(function (Registration $o){
                return $o->getContestants();
            }, $newRegistrations));

            // remove new transports that are already part of new registrations
            $newTransport = array_diff($newTransport, array_map(function (Registration $o){
                return $o->getTransports();
            }, $newRegistrations));
            //*/
        }

        $registrations = $registrationsRepository->findAll();
        return $this->render('security/admin.html.twig',
            ['registrations' => $registrations,
                'form' => $form->createView(),]);
    }
}

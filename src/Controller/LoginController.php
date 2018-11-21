<?php

namespace App\Controller;


use App\Entity\ChangeSet;
use App\Entity\Contestant;
use App\Entity\Official;
use App\Entity\Transport;
use App\Form\ChangeSetType;
use App\Form\LoginType;
use App\Form\ForgotPasswordType;
use App\Repository\ChangeSetRepository;
use App\Repository\ContestantsRepository;
use App\Repository\OfficialsRepository;
use App\Repository\RegistrationsRepository;
use App\Repository\TransportRepository;
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
     * @param $locales
     * @param $defaultLocale
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils, $locales, $defaultLocale): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // locales and defaultLocales must be used at least once in code. This block is to avoid them being maked as unused.
        if ($locales === $defaultLocale && $locales !== $defaultLocale) {
            throw $this->createNotFoundException(
                'This should never happen.'
            );
        }

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
     * @throws \Doctrine\ORM\NonUniqueResultException
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

            /*
             * filter changes (only want drops of each repository)
             */
            $officialsDrops = \array_filter($changes, function (ChangeSet $changeSet) {
                return $changeSet->getType() === 'DROP' && $changeSet->getName() === 'official';
            });
            $contestantsDrops = \array_filter($changes, function (ChangeSet $changeSet) {
                return $changeSet->getType() === 'DROP' && $changeSet->getName() === 'contestant';
            });
            $transportsDrops = \array_filter($changes, function (ChangeSet $changeSet) {
                return $changeSet->getType() === 'DROP' && $changeSet->getName() === 'transport';
            });

            /*
             * retrieve object from change set (and adjust timestamp)
             */
            $getObject = function (ChangeSet $changeSet) {
                $obj = (object)\json_decode($changeSet->getChangeSet());
                $obj->timestamp = $changeSet->getTimestamp();
                return $obj;
            };
            $officialsDrops = \array_map($getObject, $officialsDrops);
            $contestantsDrops = \array_map($getObject, $contestantsDrops);
            $transportsDrops = \array_map($getObject, $transportsDrops);

            $registrationsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newRegistrations) {
                return $changeSet->getType() === 'UPDATE' && $changeSet->getName() === 'registration' && !\in_array($changeSet->getNameId(), $newRegistrations, true);
            });
            $officialsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newOfficials) {
                return $changeSet->getType() === 'UPDATE' && $changeSet->getName() === 'official' && !\in_array($changeSet->getNameId(), $newOfficials, true);
            });
            $contestantsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newContestants) {
                return $changeSet->getType() === 'UPDATE' && $changeSet->getName() === 'contestant' && !\in_array($changeSet->getNameId(), $newContestants, true);
            });
            $transportsChanges = \array_filter($changes, function (ChangeSet $changeSet) use ($newTransports) {
                return $changeSet->getType() === 'UPDATE' && $changeSet->getName() === 'transport' && !\in_array($changeSet->getNameId(), $newTransports, true);
            });

            $setClubRegistration = function (ChangeSet $changeSet) use ($registrationsRepository) {
                $registration = $registrationsRepository->findOneById($changeSet->getNameId());
                if ($registration) {
                    $changeSet->setClub($registration->getClub() . '[' . $registration->getId() . ']');
                }
                return $changeSet;
            };
            $setClubOfficials = function (ChangeSet $changeSet) use ($officialsRepository) {
                $official = $officialsRepository->findOneById($changeSet->getNameId());
                if ($official) {
                    $registration = $official->getRegistration();
                    if ($registration) {
                        $changeSet->setClub($registration->getClub() . '[' . $registration->getId() . ']');
                    }
                }
                return $changeSet;
            };
            $setClubContestants = function (ChangeSet $changeSet) use ($contestantsRepository) {
                $contestant = $contestantsRepository->findOneById($changeSet->getNameId());
                if ($contestant) {
                    $registration = $contestant->getRegistration();
                    if ($registration) {
                        $changeSet->setClub($registration->getClub() . '[' . $registration->getId() . ']');
                    }
                }
                return $changeSet;
            };
            $setClubTransport = function (ChangeSet $changeSet) use ($transportRepository) {
                $transport = $transportRepository->findOneById($changeSet->getNameId());
                if ($transport) {
                    $registration = $transport->getRegistration();
                    if ($registration) {
                        $changeSet->setClub($registration->getClub() . '[' . $registration->getId() . ']');
                    }
                }
                return $changeSet;
            };

            $registrationsChanges = \array_map($setClubRegistration, $registrationsChanges);
            $officialsChanges = \array_map($setClubOfficials, $officialsChanges);
            $contestantsChanges = \array_map($setClubContestants, $contestantsChanges);
            $transportsChanges = \array_map($setClubTransport, $transportsChanges);

            $changes = \array_merge($registrationsChanges, $officialsChanges, $contestantsChanges, $transportsChanges);

            usort($newOfficials, function (Official $a, Official $b) {
                if (null === $a->getRegistration() && null === $b->getRegistration()) {
                    return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
                }
                return 0;
            });
            usort($newContestants, function (Contestant $a, Contestant $b) {
                if (null === $a->getRegistration() && null === $b->getRegistration()) {
                    return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
                }
                return 0;
            });
            usort($newTransports, function (Transport $a, Transport $b) {
                if (null === $a->getRegistration() && null === $b->getRegistration()) {
                    return $a->getRegistration()->getId() <=> $b->getRegistration()->getId();
                }
                return 0;
            });

            $message = (new \Swift_Message('Change history from ' . \date_format($after, 'Y-m-d H:i:s') . ' to ' . \date_format($before, 'Y-m-d H:i:s')))
                ->setFrom('anmeldung@thueringer-judoverband.de')
                ->setTo($this->getUser()->getEmail())
                ->setBody($this->renderView('emails/change_history.html.twig', [
                    'from_date' => \date_format($after, 'Y-m-d H:i:s'),
                    'to_date' => \date_format($before, 'Y-m-d H:i:s'),
                    'newRegistrations' => $newRegistrations,
                    'newOfficials' => $newOfficials,
                    'newContestants' => $newContestants,
                    'newTransports' => $newTransports,
                    'droppedOfficials' => $officialsDrops,
                    'droppedContestants' => $contestantsDrops,
                    'droppedTransports' => $transportsDrops,
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

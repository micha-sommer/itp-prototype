<?php


namespace App\Controller;


use App\Entity\Contestant;
use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Entity\InvoicePositionsList;
use App\Entity\Registration;
use App\Enum\GenderEnum;
use App\Enum\ITCEnum;
use App\Form\InvoicePositionsListType;
use App\Repository\InvoiceItemRepository;
use App\Repository\InvoiceRepository;
use App\Repository\RegistrationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use function in_array;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class InvoiceController extends AbstractController
{

    /**
     * @Route("/invoicing", name="invoicing")
     *
     *
     * @param Request $request
     * @param InvoiceRepository $invoiceRepository
     * @param InvoiceItemRepository $invoiceItemRepository
     * @return Response
     */
    public function invoicing(Request $request, InvoiceRepository $invoiceRepository, InvoiceItemRepository $invoiceItemRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Registration $registration */
        $registration = $this->getUser();
        $invoice = $invoiceRepository->findOneBy(['registration' => $registration]);

        if ($invoice === null) {

            // create initial invoice
            $invoice = new Invoice();
            $invoice->setRegistration($registration);
            $invoice->setPaidBankEuro(0);
            $invoice->setPaidCashEuro(0);

            // prepare some values
            $officials = $registration->getOfficials();
            $officials_count = $officials->count();
            $officials_male_with_ITC_count = 0;
            $officials_female_with_ITC_count = 0;
            foreach ($officials as $official) {
                if ($official->getItc() !== ITCEnum::no) {
                    if ($official->getGender() === GenderEnum::male) {
                        $officials_male_with_ITC_count++;
                    } else {
                        $officials_female_with_ITC_count++;
                    }
                }
            }

            $contestants = $registration->getContestants();
            $contestants_count = $contestants->count();
            $contestants_with_ITC_count = $contestants->filter(static function (Contestant $contestant) {
                return $contestant->getItc() !== ITCEnum::no;
            })->count();

            $totalInvoice = 0;
            foreach ($invoiceItemRepository->findAll() as $invoiceItem) {
                $invoicePosition = new InvoicePosition();
                $invoicePosition->setIsAdd(true);
                $invoicePosition->setItem($invoiceItem);
                $multiplier = 0;
                switch ($invoiceItem->getId()) {
                    case 1: // Startgeld
                        $multiplier = $contestants_count;
                        break;
                    case 2: // erhöhtes Startgeld
                        break;
                    case 3: // Mehrbettzimmer
                        $multiplier = (int)($officials_male_with_ITC_count / 2) + (int)($officials_female_with_ITC_count / 2) + $contestants_count;
                        break;
                    case 4: // Einzelzimmer
                        $multiplier = $officials_male_with_ITC_count % 2 + $officials_female_with_ITC_count % 2;
                        break;
                    case 5: // Transportpauschale
                        $multiplier = ($officials_count + $contestants_count) * $registration->getTransports()->count();
                        break;
                    case 6: // Internationales Trainings Camp
                        $multiplier = $officials_female_with_ITC_count + $officials_male_with_ITC_count + $contestants_with_ITC_count;
                        break;
                    default:
                        break;
                }
                $invoicePosition->setMultiplier($multiplier);
                $totalPosition = $invoicePosition->getMultiplier() * $invoiceItem->getAmountEuro();
                if ($invoicePosition->getIsAdd()) {
                    $totalInvoice += $totalPosition;
                } else {
                    $totalInvoice -= $totalPosition;
                }
                $invoicePosition->setTotalEuro($totalPosition);
                $invoice->addInvoicePosition($invoicePosition);
                $em->persist($invoicePosition);
            }
            $invoice->setTotalEuro($totalInvoice);
            $em->persist($invoice);
            $em->flush();
        }

        $invoicePositionsBefore = $invoice->getInvoicePositions();
        $invoicePositionsAfter = new InvoicePositionsList();
        $invoicePositionsAfter->setList(new ArrayCollection($invoicePositionsBefore->toArray()));

        if ($invoicePositionsAfter->getList()->isEmpty()) {
            {
                $invoicePosition = new InvoicePosition();
                $invoicePosition->setIsAdd(true);
                $invoicePositionsAfter->addInvoicePosition($invoicePosition);
            }
        }

        $form = $this->createForm(InvoicePositionsListType::class, $invoicePositionsAfter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            dump($invoicePositionsBefore);
            dump($invoicePositionsAfter);

            // check for deleted invoice positions
            foreach ($invoicePositionsBefore as $invoicePosition) {
                if (false === $invoicePositionsAfter->getList()->contains($invoicePosition)) {
                    $em->remove($invoicePosition);
                    $em->flush();
                }
            }

            // check for added invoice positions
            foreach ($invoicePositionsAfter->getList() as $invoicePosition) {
                if (false === in_array($invoicePosition, $invoicePositionsBefore->toArray(), true)) {
                    if (null === $invoicePosition->getInvoice()) {
                        $invoicePosition->setInvoice($invoice);
                    }
                    $em->persist($invoicePosition);
                }
            }

            // recalulate total
            $totalInvoice = 0;
            foreach ($invoice->getInvoicePositions() as $invoicePosition) {
                if ($invoicePosition->getIsAdd()) {
                    $totalInvoice += $invoicePosition->getTotalEuro();
                } else {
                    $totalInvoice -= $invoicePosition->getTotalEuro();
                }
            }
            $invoice->setTotalEuro($totalInvoice);

            $em->flush();
            if ($request->request->get('back')) {
                return $this->redirectToRoute('welcome');
            }
        }

        return $this->render('invoice/invoicing.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/invoice/{uid}", name="invoice")
     * @param int $uid
     * @param RegistrationsRepository $registrationsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function invoice(int $uid, RegistrationsRepository $registrationsRepository): Response
    {
        $registration = $registrationsRepository->findOneById($uid);

        if ($registration === null) {
            return new Response(404);
        }

        $invoice = $registration->getInvoice();

        if ($invoice === null) {
            $invoice = new Invoice();
            $invoice->setRegistration($registration);
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('Thüringer Judo-Verband e.V.');
        $pdf->setTitle('Rechnung Internationaler Thüringenpokal');
        $pdf->setSubject('Rechnung für ' . $invoice->getRegistration()->getClub());

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Auswahl des Font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // Auswahl der MArgins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // Automatisches Autobreak der Seiten
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // Image Scale
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Schriftart
        $pdf->SetFont('dejavusans', '', 10);
        // Neue Seite
        $pdf->AddPage();

        $pdf->writeHTML($this->render('invoice/invoice.html.twig', ['invoice' => $invoice])->getContent(), true, false, true);

        $pdf->Output('Rechnung.pdf');

        return new Response(200);
    }

    /**
     * @Route("/invoice/delete/{uid}", name="delete_invoice")
     * @param $uid
     * @param RegistrationsRepository $registrationsRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function delete($uid, RegistrationsRepository $registrationsRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $registration = $registrationsRepository->findOneById($uid);
        if ($registration !== null) {
            $registration->setInvoice(null);
            $em->flush();
//            $invoice = $registration->getInvoice();
//            $em->remove($invoice);
//            $em->flush();
        }
        return $this->redirectToRoute('admin', ['_switch_user' => '_exit']);
    }

}

<?php


namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Entity\Registration;
use App\Enum\GenderEnum;
use App\Enum\ITCEnum;
use App\Form\InvoiceType;
use App\Repository\ContestantsRepository;
use App\Repository\InvoiceRepository;
use App\Repository\OfficialsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class InvoiceController extends AbstractController
{

    /**
     * @Route("/new_invoice", name="new_invoice")
     *
     * @param OfficialsRepository $officialsRepository
     * @param ContestantsRepository $contestantsRepository
     * @return Response
     * @noinspection PhpUnused
     */
    public function newInvoice(OfficialsRepository $officialsRepository, ContestantsRepository $contestantsRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Registration $registration */
        $registration = $this->getUser();

        $invoice = new Invoice();
        $invoice->setTotal(0); // default
        $invoice->setPublished(false);
        $invoice->setInvoiceAddress($registration->getInvoiceAddress());
        $invoice->setSubId($registration->getInvoices()->count());
        $invoice->setName($invoice->getSubId());

        if ($registration->getInvoices()->isEmpty()) {
            // prepare counters values
            // officials
            $officials_count = $officialsRepository->count(['registration' => $registration]);
            // male
            $officials_male_friday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::male, 'friday' => true]);
            $officials_male_saturday_no_itc = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::male, 'saturday' => true, 'itc' => ITCEnum::no]);
            $officials_male_with_itc_tuesday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::male, 'itc' => ITCEnum::tillTuesday]);
            $officials_male_with_itc_wednesday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::male, 'itc' => ITCEnum::tillWednesday]);
            // female
            $officials_female_friday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::female, 'friday' => true]);
            $officials_female_saturday_no_itc = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::female, 'saturday' => true, 'itc' => ITCEnum::no]);
            $officials_female_with_itc_tuesday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::female, 'itc' => ITCEnum::tillTuesday]);
            $officials_female_with_itc_wednesday = $officialsRepository->count(['registration' => $registration, 'gender' => GenderEnum::female, 'itc' => ITCEnum::tillWednesday]);

            // contestants
            $contestants_count = $contestantsRepository->count(['registration' => $registration]);
            $contestants_friday = $contestantsRepository->count(['registration' => $registration, 'friday' => true]);
            $contestants_saturday_no_itc = $contestantsRepository->count(['registration' => $registration, 'saturday' => true, 'itc' => ITCEnum::no]);
            $contestants_with_itc_tuesday = $contestantsRepository->count(['registration' => $registration, 'itc' => ITCEnum::tillTuesday]);
            $contestants_with_itc_wednesday = $contestantsRepository->count(['registration' => $registration, 'itc' => ITCEnum::tillWednesday]);

            $invoiceTotal = 0;

            // add default Startgeld
            $entry_fee = $contestants_count * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Startgeld (entry fee)');
            $invoicePosition->setMultiplier($entry_fee);
            $invoicePosition->setPrice(3000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add default erhöhtes Startgeld
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('erhöhtes Startgeld (increased entry fee)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(6000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Einzelzimmer
            $single_room_fee = (
                    $officials_male_friday % 2 +
                    $officials_male_saturday_no_itc % 2 +
                    $officials_female_friday % 2 +
                    $officials_female_saturday_no_itc % 2
                ) * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Einzelzimmer (single room)');
            $invoicePosition->setMultiplier($single_room_fee);
            $invoicePosition->setPrice(5500);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Mehrbettzimmer
            $shared_room_fee = (
                    (int)($officials_male_friday / 2) +
                    (int)($officials_male_saturday_no_itc / 2) +
                    (int)($officials_female_friday / 2) +
                    (int)($officials_female_saturday_no_itc / 2) +
                    $contestants_friday +
                    $contestants_saturday_no_itc
                ) * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Mehrbettzimmer (shared rooms, dorm)');
            $invoicePosition->setMultiplier($shared_room_fee);
            $invoicePosition->setPrice(4500);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Transportpauschale
            $tranport_fee = ($officials_count + $contestants_count) * $registration->getTransports()->count() * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Transportpauschale (transfer airport)');
            $invoicePosition->setMultiplier($tranport_fee);
            $invoicePosition->setPrice(8000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Internationales Trainings Camp Tuesday edition
            $itc_till_tuesday_fee = ($officials_female_with_itc_tuesday + $officials_male_with_itc_tuesday + $contestants_with_itc_tuesday) * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Internationales Trainings Camp');
            $invoicePosition->setMultiplier($itc_till_tuesday_fee);
            $invoicePosition->setPrice(25000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Internationales Trainings Camp Wednesday edition
            $itc_till_wednesday_fee = ($officials_female_with_itc_wednesday + $officials_male_with_itc_wednesday + $contestants_with_itc_wednesday) * 100;
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Internationales Trainings Camp');
            $invoicePosition->setMultiplier($itc_till_wednesday_fee);
            $invoicePosition->setPrice(28000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            $invoice->setTotal($invoiceTotal);
        } else {
            // add default Startgeld
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Startgeld (entry fee)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(3000);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add default erhöhtes Startgeld
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('erhöhtes Startgeld (increased entry fee)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(6000);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Einzelzimmer
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Einzelzimmer (single room)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(5500);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Mehrbettzimmer
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Mehrbettzimmer (shared rooms, dorm)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(4500);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Transportpauschale
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Transportpauschale (transfer airport)');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(8000);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Internationales Trainings Camp Tuesday edition
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Internationales Trainings Camp');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(25000);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Internationales Trainings Camp Wednesday edition
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Internationales Trainings Camp');
            $invoicePosition->setMultiplier(0);
            $invoicePosition->setPrice(28000);
            $invoicePosition->calculateTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);
        }
        $registration->addInvoice($invoice);

        $em->persist($invoice);
        $em->flush();


        return $this->redirectToRoute('invoicing', ['uid' => $invoice->getId()]);
    }

    /**
     * @Route("/invoicing/{uid}", name="invoicing")
     *
     * @param Request $request
     * @param int $uid
     * @param InvoiceRepository $invoiceRepository
     * @return Response
     */
    public function invoicing(Request $request, $uid, InvoiceRepository $invoiceRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $invoice = $invoiceRepository->find($uid);
        if ($invoice === null) {
            throw $this->createNotFoundException(
                'Error: Invoice with id ' . $uid . ' could not be found.'
            );
        }

        $form = $this->createForm(InvoiceType::class, $invoice);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->get('publish')) {
                $invoice->setPublished(!$invoice->getPublished());
            }

            $invoice->calculateTotal();
            $em->flush();
        }

        return $this->render('invoice/invoicing.html.twig', [
            'form' => $form->createView(),
            'invoice' => $invoice
        ]);
    }

    /**
     * @Route("/invoice/{uid}", name="invoice")
     * @param int $uid
     * @param InvoiceRepository $invoiceRepository
     * @return Response
     */
    public function invoice(int $uid, InvoiceRepository $invoiceRepository): Response
    {
        $invoice = $invoiceRepository->find($uid);

        if ($invoice === null) {
            throw $this->createNotFoundException(
                'Error: Invoice with id ' . $uid . ' could not be found.'
            );
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('Thüringer Judo-Verband e.V.');
        $pdf->setTitle('Rechnung Internationaler Thüringenpokal');
        /** @noinspection NullPointerExceptionInspection */
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
     * @param InvoiceRepository $invoiceRepository
     * @return Response
     */
    public function delete($uid, InvoiceRepository $invoiceRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $invoice = $invoiceRepository->find($uid);

        $em->remove($invoice);
        $em->flush();

        return $this->redirectToRoute('admin', ['_switch_user' => '_exit']);
    }

}

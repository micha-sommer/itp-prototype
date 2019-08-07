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
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use function in_array;
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
     * @return Response
     */
    public function newInvoice(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $invoice = new Invoice();
        $invoice->setPublished(false);
        $invoice->setTotal(0); // default

        /** @var Registration $registration */
        $registration = $this->getUser();
        if ($registration->getInvoices()->isEmpty()) {
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

            $invoiceTotal = 0;

            // add default Startgeld
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Startgeld (entry fee)');
            $invoicePosition->setMultiplier($contestants_count);
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
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Einzelzimmer (single room)');
            $invoicePosition->setMultiplier($officials_male_with_ITC_count % 2 + $officials_female_with_ITC_count % 2);
            $invoicePosition->setPrice(5000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Mehrbettzimmer
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Mehrbettzimmer (shared rooms, dorm)');
            $invoicePosition->setMultiplier((int)($officials_male_with_ITC_count / 2) + (int)($officials_female_with_ITC_count / 2) + $contestants_count);
            $invoicePosition->setPrice(4000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Transportpauschale
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Transportpauschale (transfer airport)');
            $invoicePosition->setMultiplier(($officials_count + $contestants_count) * $registration->getTransports()->count());
            $invoicePosition->setPrice(8000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            // add Internationales Trainings Camp
            $invoicePosition = new InvoicePosition();
            $invoicePosition->setDescription('Internationales Trainings Camp');
            $invoicePosition->setMultiplier($officials_female_with_ITC_count + $officials_male_with_ITC_count + $contestants_with_ITC_count);
            $invoicePosition->setPrice(28000);
            $invoiceTotal += $invoicePosition->calculateTotal()->getTotal();
            $invoicePosition->setInvoice($invoice);
            $em->persist($invoicePosition);

            $invoice->setTotal($invoiceTotal);
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
        if($invoice === null)
        {
            throw $this->createNotFoundException(
                'Error: Invoice with id '.$uid.' could not be found.'
            );
        }

        $invoicePositionsBefore = $invoice->getInvoicePositions();
        $invoicePositionsAfter = new InvoicePositionsList();
        $invoicePositionsAfter->setList(new ArrayCollection($invoicePositionsBefore->toArray()));

        if ($invoicePositionsAfter->getList()->isEmpty()) {
            {
                $invoicePosition = new InvoicePosition();
                $invoicePositionsAfter->addInvoicePosition($invoicePosition);
            }
        }

        $form = $this->createForm(InvoicePositionsListType::class, $invoicePositionsAfter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

            if ($request->request->get('publish')) {
                $invoice->setPublished(true);
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

        if($invoice === null)
        {
            throw $this->createNotFoundException(
                'Error: Invoice with id '.$uid.' could not be found.'
            );
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

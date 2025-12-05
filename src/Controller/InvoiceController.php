<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Form\InvoiceType;
use App\Repository\ContestantRepository;
use App\Repository\OfficialRepository;
use App\Repository\RegistrationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use TCPDF;
use function round;

#[Route('/{_locale<%app.supported_locales%>}/invoices')]
class InvoiceController extends AbstractController
{
    #[Route('/new', name: 'invoice_new')]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        RegistrationRepository $registrationRepository,
        ContestantRepository   $contestantRepository,
        OfficialRepository     $officialRepository,
    ): Response
    {
        $registrationId = $request->query->get('registrationId');

        $registration = $registrationRepository->find($registrationId);

        $invoice = new Invoice();
        $invoice->setName($registration->getFirstName() . " " . $registration->getLastName());
        $invoice->setTotalInHundreds(0);
        $invoice->setInvoiceAddress($registration->getInvoiceAddress());
        $invoice->setRegistration($registration);
        $invoice->setPublished(false);

        $contestantCount = $contestantRepository->getRegularContestantCount($registration);
        $lateCount = $contestantRepository->getLateContestantCount($registration);
        $this->createInvoicePosition($invoice, 'Startgeld (entry fee)', $contestantCount * 100, 5000);
        $this->createInvoicePosition($invoice, 'erhöhtes Startgeld (increased entry fee)', $lateCount * 100, 8000);

        $this->createInvoicePosition($invoice, 'ITC: Paket A EZ (package A single)', 0, 25500);
        $this->createInvoicePosition($invoice, 'ITC: Paket B EZ (package B single)', 0, 33500);
        $this->createInvoicePosition($invoice, 'ITC: Paket C EZ (package C single)', 0, 41500);
        $this->createInvoicePosition($invoice, 'ITC: Paket D EZ (package D single)', 0, 51000);

        $packACount = $contestantRepository->getPackACount($registration) + $officialRepository->getPackACount($registration);
        $packBCount = $contestantRepository->getPackBCount($registration) + $officialRepository->getPackBCount($registration);
        $packCCount = $contestantRepository->getPackCCount($registration) + $officialRepository->getPackCCount($registration);
        $packDCount = $contestantRepository->getPackDCount($registration) + $officialRepository->getPackDCount($registration);
        $this->createInvoicePosition($invoice, 'ITC: Paket A DZ/MBZ (package A shared)', $packACount * 100, 21500);
        $this->createInvoicePosition($invoice, 'ITC: Paket B DZ/MBZ (package B shared)', $packBCount * 100, 29500);
        $this->createInvoicePosition($invoice, 'ITC: Paket C DZ/MBZ (package C shared)', $packCCount * 100, 37000);
        $this->createInvoicePosition($invoice, 'ITC: Paket D DZ/MBZ (package D shared)', $packDCount * 100, 45000);

        $this->createInvoicePosition($invoice, 'Transportpauschale (transfer airport)', 0, 8000);

        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }

    private function createInvoicePosition(
        Invoice $invoice,
        string  $description,
        int     $amountInHundreds,
        int     $priceInHundreds,
    ): void
    {
        $position = new InvoicePosition();

        $position->setDescription($description);
        $position->setAmountInHundreds($amountInHundreds);
        $position->setPriceInHundreds($priceInHundreds);

        $roundedAmount = round($position->getAmountInHundreds() / 100, 2);
        $position->setTotalInHundreds($position->getPriceInHundreds() * $roundedAmount);

        $invoice->addInvoicePosition($position);
    }

    #[Route('/{id}/edit', name: 'invoice_edit')]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'invoice_show', methods: "GET")]
    public function show(Invoice $invoice): Response
    {
        if ($this->getUser() !== $invoice->getRegistration()) {
            $this->denyAccessUnlessGranted("ROLE_ADMIN");
        }

        $pdf = $this->getInvoicePDF($invoice);

        $pdf->Output('Rechnung.pdf');

        return new Response(200);
    }

    /**
     * @param Invoice $invoice
     * @return TCPDF
     */
    private function getInvoicePDF(Invoice $invoice): TCPDF
    {
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
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // Image Scale
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Schriftart
        $pdf->SetFont('dejavusans', '', 10);
        // Neue Seite
        $pdf->AddPage();

        $html = $this->render('invoice/pdf.html.twig', ['invoice' => $invoice])->getContent();
        $pdf->writeHTML($html, true, false, true);
        return $pdf;
    }

    #[Route('/{id}', name: 'invoice_delete', methods: "POST")]
    public function delete(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/mail', name: 'invoice_send_mail')]
    public function sendInvoicePerMail(
        Request             $request,
        Invoice             $invoice,
        MailerInterface     $mailer,
        TranslatorInterface $translator,
    ): Response
    {
        $timestamp = new DateTime();
        $registration = $invoice->getRegistration();
        $name = $translator->trans('global.tjv-name');
        $subject = $translator->trans('invoice.mail.subject', [
            'club' => $registration->getClub(),
        ]);
        $title = $translator->trans('invoice.mail.title', [
            'name' => $registration->getFirstName() . ' ' . $registration->getLastName()
        ]);
        $greeting = $translator->trans('invoice.mail.greeting', [
            'timestamp' => $timestamp,
        ]);

        $pdf = $this->getInvoicePDF($invoice);
        $data = $pdf->Output('Invoice_' . $invoice->getName() . '.pdf', 'S');
        $mail = (new TemplatedEmail())
            ->from(new Address('anmeldung@thueringer-judoverband.de', $name))
            ->to($registration->getEmail())
            ->subject($subject)
            ->context([
                'title' => $title,
                'greeting' => $greeting,
                'invoice' => $invoice,
                'requestLocale' => $request->getLocale(),
            ])
            ->htmlTemplate('invoice/email.html.twig')
            ->attach($data, 'Invoice_' . $invoice->getName() . '.pdf', 'application/pdf');

        $mailer->send($mail);

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }

    #[Route('/{id}/publish', name: 'invoice_publish')]
    public function publishInvoice(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $invoice->setPublished(true);
        $entityManager->flush();

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }

    #[Route('/{id}/hide', name: 'invoice_hide')]
    public function hideInvoice(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $invoice->setPublished(false);
        $entityManager->flush();

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }
}

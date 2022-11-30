<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Form\InvoiceType;
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

        $this->createInvoicePosition($invoice, 'Startgeld (entry fee)', 0, 5000);
        $this->createInvoicePosition($invoice, 'erhöhtes Startgeld (increased entry fee)', 0, 8000);

        $this->createInvoicePosition($invoice, 'ITC: Paket A EZ (package A single)', 0, 26000);
        $this->createInvoicePosition($invoice, 'ITC: Paket B EZ (package B single)', 0, 32000);
        $this->createInvoicePosition($invoice, 'ITC: Paket C EZ (package C single)', 0, 38000);

        $this->createInvoicePosition($invoice, 'ITC: Paket A DZ/MBZ (package A shared)', 0, 24000);
        $this->createInvoicePosition($invoice, 'ITC: Paket B DZ/MBZ (package B shared)', 0, 30000);
        $this->createInvoicePosition($invoice, 'ITC: Paket C DZ/MBZ (package C shared)', 0, 36000);

        $this->createInvoicePosition($invoice, 'ITC: 1 Tag (1 day)', 0, 4000);
        $this->createInvoicePosition($invoice, 'ITC: 2 Tage (2 days)', 0, 8000);
        $this->createInvoicePosition($invoice, 'ITC: 3 Tage (3 days)', 0, 11000);

        $this->createInvoicePosition($invoice, 'ITC: 1 Tag Trainer (1 day trainer)', 0, 1500);
        $this->createInvoicePosition($invoice, 'ITC: 2 Tage Trainer (2 days trainer)', 0, 4000);
        $this->createInvoicePosition($invoice, 'ITC: 3 Tage Trainer (3 days trainer)', 0, 6000);

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
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
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
        Request $request,
        Invoice $invoice,
        MailerInterface $mailer,
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
            'name' => $registration->getFirstName().' '. $registration->getLastName()
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
            ->attach($data, 'Invoice_' . $invoice->getName() . '.pdf', 'application/pdf')
        ;

        $mailer->send($mail);

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }
}

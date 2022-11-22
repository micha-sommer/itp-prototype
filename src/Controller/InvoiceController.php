<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoicePosition;
use App\Form\InvoiceType;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    #[Route('/{id}', name: 'invoice_delete', methods: "POST")]
    public function delete(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }
}

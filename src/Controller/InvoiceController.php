<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    /**
     * @Route("/invoice", name="invoice")
     * @return Response
     */
    public function invoice(): Response
    {
        return $this->render('invoice/invoice.html.twig');
    }
}
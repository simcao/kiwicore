<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */


namespace App\Controller\Eshop;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Form\InvoiceItemType;
use App\Form\InvoiceType;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage stock related pages.
 *
 * @author Simcao EI
 */
class InvoiceController extends AbstractController
{
    /**
     * Return page with all invoices result
     *
     * @param ManagerRegistry $doctrine
     * @param int $currentPage
     * @return Response
     */
    #[Route('/admin/factures/liste/{currentPage}', name: 'kiwicore_invoice')]
    public function listInvoices(ManagerRegistry $doctrine, int $currentPage = 1): Response
    {
        $invoices = $doctrine->getRepository(Invoice::class)->findAllByDatePaginated($currentPage);

        return $this->render('modules/eshop/invoices/index.html.twig', [
            'invoices' => $invoices,
            'currentPage' => $currentPage,
            'maxPage' => $invoices->totalPages
        ]);
    }

    /**
     * Return page with form to create a new invoice.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/factures/ajouter-une-facture', name: 'kiwicore_invoice_create')]
    public function createInvoice(ManagerRegistry $doctrine, Request $request): Response
    {
        $invoice = new Invoice();

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $invoice = $form->getData();

            $invoice->setCreatedAt(new DateTime());
            $invoice->setUpdatedAt(new DateTime());
            $invoice->setState(0);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($invoice);
            $entityManager->flush();

            $this->addFlash('success', 'Une nouvelle facture a été créée pour ' . $invoice->getCustomer()->getName());

            return $this->redirectToRoute('kiwicore_invoice');
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des factures</li><li>ajouter une facture</li>",
            'page_title' => 'Formulaire de création de facture',
            'form' => $form,
        ]);
    }

    /**
     * Return page with details of invoice. Redirect to invoice list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/admin/factures/{id}', name: 'kiwicore_invoice_show')]
    public function showInvoice(ManagerRegistry $doctrine, int $id): Response
    {
        $invoice = $doctrine->getRepository(Invoice::class)->findOneWithDetails($id);

        if (!$invoice)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver cette facture.');
            return $this->redirectToRoute('kiwicore_invoice');
        }

        return $this->render('modules/eshop/invoices/show.html.twig', [
            'invoice' => $invoice
        ]);

    }

    /**
     * Return page with form to add invoice items.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/admin/factures/{id}/ajouter-un-produit', name: 'kiwicore_invoice_add_item')]
    public function addInvoiceItem(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $invoiceItem = new InvoiceItem();
        $invoice = $doctrine->getRepository(Invoice::class)->find($id);

        if (!$invoice || $invoice->getState() != 0)
        {
            $this->addFlash('error', 'Une erreur est survenue. Cette facture ne peut pas être modifiée.');
            return $this->redirectToRoute('kiwicore_invoice');
        }

        $form = $this->createForm(InvoiceItemType::class, $invoiceItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $invoiceItem = $form->getData();
            $invoiceItem->setInvoice($invoice);
            $invoiceItem->setPrice(
                $invoiceItem->getProduct()->getPrice()
            );

            $entityManager = $doctrine->getManager();
            $entityManager->persist($invoiceItem);
            $entityManager->flush();

            $this->addFlash('success', 'La facture a été mise à jour.');
            return $this->redirectToRoute('kiwicore_invoice_show', [
                'id' => $invoice->getId()
            ]);
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des factures</li><li>ajouter une facture</li>",
            'page_title' => 'Formulaire de création de facture',
            'form' => $form,
        ]);
    }

    /**
     * Force PDF download of invoice. Redirect to invoices list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param Pdf $knpSnappyPdf
     * @param int $id
     * @return Response
     */
    #[Route('/admin/factures/pdf/{id}', name: 'kiwicore_invoice_download')]
    public function downloadInvoice(ManagerRegistry $doctrine, Pdf $knpSnappyPdf, int $id): Response
    {
        $invoice = $doctrine->getRepository(Invoice::class)->find($id);

        if (!$invoice)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver cette facture.');
            return $this->redirectToRoute('kiwicore_invoice');
        }

        $html = $this->renderView('pdf/invoice.html.twig', [
            'invoice' => $invoice
        ]);

        header('Content-Type: application/pdf');

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html), 'Facture' . $invoice->getId() . '.pdf'
        );

    }
}
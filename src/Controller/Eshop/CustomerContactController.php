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

use App\Entity\Customer;
use App\Entity\CustomerContact;
use App\Form\CustomerContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage customers' contact related pages
 *
 * @author Simcao EI
 */
class CustomerContactController extends AbstractController {

    /**
     * Return page with form to create a new customer contact.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/clients/ajouter-un-contact/{id}', name: 'kiwicore_customer_contact_create')]
    public function createCustomerContact(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $customer = $doctrine->getRepository(Customer::class)->find($id);

        if(!$customer)
        {
            $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        $customerContact = new CustomerContact();
        $customerContact->setCustomer($customer);

        $form = $this->createForm(CustomerContactType::class, $customerContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($customerContact);
            $entityManager->flush();

            $this->addFlash('success', "Le contact a bien été enregistré");
            return $this->redirectToRoute('kiwicore_customer_show', ['id' => $customer->getId()]);
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des clients</li><li>ajouter un contact</li>",
            'page_title' => 'Formulaire de gestion client',
            'form' => $form
        ]);
    }

    /**
     * Return page to edit a customer contact. Redirect to customer list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/admin/clients/modifier-un-contact/{id}', name: 'kiwicore_customer_contact_edit')]
    public function editCustomerContact(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $customerContact = $doctrine->getRepository(CustomerContact::class)->find($id);

        if(!$customerContact)
        {
            $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        $form = $this->createForm(CustomerContactType::class, $customerContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($customerContact);
            $entityManager->flush();

            $this->addFlash('success', "Le contact a été modifié avec succès.");
            return $this->redirectToRoute('kiwicore_customer_show', ['id' => $customerContact->getCustomer()->getId()]);
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des clients</li><li>modifier un contact</li>",
            'page_title' => 'Formulaire de gestion client',
            'form' => $form
        ]);
    }

    /**
     * Route to delete a customer contact. Redirect to customer list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @param int $customerId
     * @return Response
     */
    #[Route('/admin/clients/supprimer-un-contact/{id}/{customerId}', name: 'kiwicore_customer_contact_delete')]
    public function deleteCustomerContact(ManagerRegistry $doctrine, int $id, int $customerId): Response
    {
        $customerContact = $doctrine->getRepository(CustomerContact::class)->find($id);
        $customer = $doctrine->getRepository(Customer::class)->find($customerId);

        if(!$customerContact && !$customer)
        {
            $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        $doctrine->getRepository(CustomerContact::class)->remove($customerContact, true);
        return $this->redirectToRoute('kiwicore_customer_show', ['id' => $customer->getId()]);
    }

}
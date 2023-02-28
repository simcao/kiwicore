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
use App\Form\CustomerType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Manage customer related pages
 *
 * @author Simcao EI
 */
class CustomerController extends AbstractController
{
    /**
     * Return page with all customers result
     *
     * @param ManagerRegistry $doctrine
     * @param int $currentPage
     * @return Response
     */
    #[Route('/admin/clients/liste/{currentPage}', name: 'kiwicore_customer')]
    public function listCustomers(ManagerRegistry $doctrine, int $currentPage = 1): Response
    {
        $customers = $doctrine->getRepository(Customer::class)->findAllByNamePaginated($currentPage);

        return $this->render('modules/eshop/customers/index.html.twig', [
            'customers' => $customers,
            'currentPage' => $currentPage,
            'maxPage' => $customers->totalPages
        ]);
    }

    /**
     * Return page with form to create a new customer.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/admin/clients/ajouter-un-client', name: 'kiwicore_customer_create')]
    public function createCustomer(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $customer = $form->getData();

            $errors = $validator->validate($customer);

            if(count($errors) > 0)
            {
                $this->addFlash('error', 'Veuillez vérifier les informations saisies.');
            }
            else
            {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($customer);
                $entityManager->flush();

                $this->addFlash('success', 'Le client ' . $customer->getName() . ' a été ajouté avec succès.');
                return $this->redirectToRoute('kiwicore_customer');
            }

        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des clients</li><li>ajouter un client</li>",
            'page_title' => 'Formulaire de gestion client',
            'form' => $form
        ]);
    }

    /**
     * Return page with details of customer. Redirect to customer list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/admin/clients/{id}', name: 'kiwicore_customer_show')]
    public function showCustomer(ManagerRegistry $doctrine, int $id): Response
    {
        $customer = $doctrine->getRepository(Customer::class)->find($id);

        if (!$customer)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce client.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        return $this->render('modules/eshop/customers/show.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * Return page to edit a customer. Redirect to customer list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param int $id
     * @return Response
     */
    #[Route('/admin/clients/modifier-un-client/{id}', name: 'kiwicore_customer_edit')]
    public function editCustomer(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator, int $id): Response
    {
        $customer = $doctrine->getRepository(Customer::class)->find($id);

        if (!$customer)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce client.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $errors = $validator->validate($customer);

            if(count($errors) > 0)
            {
                $this->addFlash('error', 'Veuillez vérifier les informations saisies.');
            }
            else
            {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($customer);
                $entityManager->flush();

                $this->addFlash('success', 'Le client ' . $customer->getName() . ' a été modifié avec succès.');
                return $this->redirectToRoute('kiwicore_customer_show', [
                    'id' => $customer->getId()
                ]);
            }
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des clients</li><li>ajouter un client</li>",
            'page_title' => 'Formulaire de gestion client',
            'form' => $form
        ]);
    }

    /**
     * Route to delete a customer. Redirect to customer list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/admin/clients/supprimer-un-client/{id}', name: 'kiwicore_customer_delete')]
    public function deleteCustomer(ManagerRegistry $doctrine, int $id): Response
    {
        $customer = $doctrine->getRepository(Customer::class)->find($id);

        if (!$customer)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce client.');
            return $this->redirectToRoute('kiwicore_customer');
        }

        $doctrine->getRepository(Customer::class)->remove($customer, true);

        $this->addFlash('success', 'Le client ' . $customer->getName() . ' a été supprimé.');
        return $this->redirectToRoute('kiwicore_customer');
    }
}
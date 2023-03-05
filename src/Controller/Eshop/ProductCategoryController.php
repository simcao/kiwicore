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

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage product category related pages.
 *
 * @author Simcao
 */
class ProductCategoryController extends AbstractController
{
    /**
     * Return page with all categories result
     *
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/admin/produits/categories', name: 'kiwicore_product_category')]
    public function listProductCategory(ManagerRegistry $doctrine): Response
    {

        $productCategories = $doctrine->getRepository(ProductCategory::class)->findAllByName();

        return $this->render('modules/eshop/products_categories/index.html.twig', [
            'categories' => $productCategories
        ]);
    }

    /**
     * Return page with form to create a new product category.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/produits/categories/ajouter-une-categorie', name: 'kiwicore_product_category_create')]
    public function createProductCategory(ManagerRegistry $doctrine, Request $request): Response
    {
        $productCategory = new ProductCategory();

        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $productCategory = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($productCategory);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie de produit ' . $productCategory->getName(). ' a été ajoutée avec succès.');
            return $this->redirectToRoute('kiwicore_product_category');
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des produits</li><li>ajouter une catégorie</li>",
            'page_title' => 'Formulaire de gestion des catégories de produit',
            'form' => $form,
        ]);
    }

    /**
     * Return page to edit a product category. Redirect to product category list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/admin/produits/categories/modifier-une-categorie/{id}', name: 'kiwicore_product_category_edit')]
    public function editProductCategory(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $productCategory = $doctrine->getRepository(ProductCategory::class)->find($id);

        if (!$productCategory)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver cette catégorie de produit.');
            return $this->redirectToRoute('kiwicore_product_category');
        }

        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $productCategory = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($productCategory);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie de produit ' . $productCategory->getName(). ' a été modifiée avec succès.');
            return $this->redirectToRoute('kiwicore_product_category');
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des produits</li><li>ajouter une catégorie</li>",
            'page_title' => 'Formulaire de gestion des catégories de produit',
            'form' => $form,
        ]);
    }

    /**
     * Route to delete a product category. Redirect to product list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/admin/produits/categories/supprimer-une-categorie/{id}', name: 'kiwicore_product_category_delete')]
    public function deleteProductCategory(ManagerRegistry $doctrine, int $id): Response
    {
        $productCategory = $doctrine->getRepository(ProductCategory::class)->find($id);

        if (!$productCategory) {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver cette catégorie de produit.');
            return $this->redirectToRoute('kiwicore_product_category');
        }

        foreach ($productCategory->getProducts() as $product)
        {
            $product->setCategory(null);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
        }

        $doctrine->getRepository(ProductCategory::class)->remove($productCategory, true);

        $this->addFlash('success', 'La catégorie de produit ' . $productCategory->getName(). ' a été supprimée avec succès.');
        return $this->redirectToRoute('kiwicore_product_category');

    }
}
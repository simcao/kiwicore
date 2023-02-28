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

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\ProductImageType;
use App\Form\ProductType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage product related pages.
 */
class ProductController extends AbstractController
{

    /**
     * Return page with all customers result
     *
     * @param ManagerRegistry $doctrine
     * @param int $currentPage
     * @return Response
     */
    #[Route('/admin/produits/liste/{currentPage}', name: 'kiwicore_product')]
    public function listProducts(ManagerRegistry $doctrine, int $currentPage = 1): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAllByNamePaginated($currentPage);

        return $this->render('modules/eshop/products/index.html.twig', [
            'products' => $products,
            'currentPage' => $currentPage,
            'maxPage' => $products->totalPages
        ]);
    }

    /**
     * Return page with form to create a new product.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/produits/ajouter-un-produit', name: 'kiwicore_product_create')]
    public function createProduct(ManagerRegistry $doctrine, Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();
            $product->setReference(uniqid());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit ' . $product->getName(). ' a été ajouté avec succès.');
            return $this->redirectToRoute('kiwicore_product');
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des produits</li><li>ajouter un produit</li>",
            'page_title' => 'Formulaire de gestion produit',
            'form' => $form
        ]);
    }

    /*
     * Return page with details of product. Redirect to product list if not found.
     */
    #[Route('/admin/produits/{id}', name: 'kiwicore_product_show')]
    public function showProduct(ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        if(!$product)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce produit.');
            return $this->redirectToRoute('kiwicore_product');
        }

        return $this->render('modules/eshop/products/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * Return page to edit a product. Redirect to product list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/admin/produits/modifier-un-produit/{id}', name: 'kiwicore_product_edit')]
    public function editProduct(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        if(!$product)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce produit.');
            return $this->redirectToRoute('kiwicore_product');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit ' . $product->getName(). ' a été modifié avec succès.');
            return $this->redirectToRoute('kiwicore_product');
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des produits</li><li>modifier un produit</li>",
            'page_title' => 'Formulaire de gestion produit',
            'form' => $form
        ]);
    }

    /**
     * Route to delete a product. Redirect to product list if not found.
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/admin/produits/supprimer-un-produit/{id}', name: 'kiwicore_product_delete')]
    public function deleteProduct(ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        if (!$product)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce produit.');
            return $this->redirectToRoute('kiwicore_product');
        }

        $doctrine->getRepository(Product::class)->remove($product, true);

        $this->addFlash('success', 'Le produit ' . $product->getName() . ' a été supprimé.');
        return $this->redirectToRoute('kiwicore_product');
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param ImageResizer $imageResizer
     * @param int $id
     * @return Response
     */
    #[Route('/admin/produits/ajouter-une-image/{id}', name: 'kiwicore_product_image_create')]
    public function createProductImage(ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader, ImageResizer $imageResizer, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);
        $productImage = new ProductImage();

        if (!$product)
        {
            $this->addFlash('error', 'Une erreur est survenue : impossible de trouver ce produit.');
            return $this->redirectToRoute('kiwicore_product');
        }

        $form = $this->createForm(ProductImageType::class, $productImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $productImage = $form->getData();

            $uploaded_file = $form->get('file')->getData();
            if ($uploaded_file)
            {
                $uploaded_file_name = $fileUploader->upload($uploaded_file, $this->getParameter('kernel.project_dir') . '/public/uploads/');
                $imageResizer->resize($this->getParameter('kernel.project_dir') . '/public/uploads/' . $uploaded_file_name);
                $productImage->setFile($uploaded_file_name);
                $productImage->setProduct($product);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($productImage);
                $entityManager->flush();

                $this->addFlash('success', 'Image ajoutée au produit ' . $product->getName());
                return  $this->redirectToRoute('kiwicore_product_show', ['id' => $product->getId()]);

            }
        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des produits</li><li>charger une image</li>",
            'page_title' => 'Charger une image',
            'form' => $form
        ]);
    }

    #[Route('/admin/produits/supprimer-une-image/{id}', name: 'kiwicore_product_image_delete')]
    public function deleteProductImage(ManagerRegistry $doctrine, int $id): Response
    {
        $productImage = $doctrine->getRepository(ProductImage::class)->find($id);

        if(!$productImage)
        {
            $this->addFlash('error', 'Une erreur est survenue, impossible de trouver le fichier demandé.');
            return $this->redirectToRoute('kiwicore_product');
        }

        $doctrine->getRepository(ProductImage::class)->remove($productImage, true);

        $this->addFlash('success', 'Cette image a été supprimée');
        return $this->redirectToRoute('kiwicore_product_show', [
            'id' => $productImage->getProduct()->getId()
        ]);

    }
}

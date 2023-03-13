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
use App\Entity\ProductStockTransaction;
use App\Form\ProductStockTransactionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage stock related pages.
 *
 * @author Simcao
 */
class StockController extends AbstractController
{
    #[Route('/admin/stocks/{currentPage}', name: 'kiwicore_stock')]
    public function listStock(ManagerRegistry $doctrine, int $currentPage = 1): Response
    {

        $products = $doctrine->getRepository(Product::class)->findAllByStockPaginated($currentPage);

        return $this->render('modules/eshop/stocks/index.html.twig', [
            'products' => $products,
            'currentPage' => $currentPage,
            'maxPage' => $products->totalPages
        ]);
    }

    #[Route('/admin/stocks/modifier-stock/{id}', name: 'kiwicore_stock_edit')]
    public function editStock(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);
        $stockTransaction = new ProductStockTransaction();

        if (!$product)
        {
            $this->addFlash('error', 'Erreur, le produit demandé n\'a pas été trouvé.');
            return $this->redirectToRoute('kiwicore_stock');
        }
        if (!$product->isStockable())
        {
            $this->addFlash('error', 'Erreur, le produit n\'est pas stockable.');
            return $this->redirectToRoute('kiwicore_stock');
        }

        $form = $this->createForm(ProductStockTransactionType::class, $stockTransaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $stockTransaction = $form->getData();
            $stockTransaction->setProduct($product);

            $product->setStock($product->getStock() + $stockTransaction->getQuantity());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($stockTransaction);
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le stock de ' . $product->getName() . ' a été mis à jour.');
            return $this->redirectToRoute('kiwicore_stock');

        }

        return $this->render('forms/form.html.twig', [
            'page_breadcrumbs' => "<li>accueil</li><li>gestion des stocks</li><li>mettre à jour les stocks</li>",
            'page_title' => 'Formulaire de gestion des stocks',
            'form' => $form,
        ]);
    }

    #[Route('/admin/stocks/produits/{id}', name: 'kiwicore_stock_show')]
    public function showStock(ManagerRegistry $doctrine, Request $request, int $id): Response
    {

        $product = $doctrine->getRepository(Product::class)->find($id);

        if (!$product)
        {
            $this->addFlash('error', 'Erreur, le produit demandé n\'a pas été trouvé.');
            return $this->redirectToRoute('kiwicore_stock');
        }
        if (!$product->isStockable())
        {
            $this->addFlash('error', 'Erreur, le produit n\'est pas stockable.');
            return $this->redirectToRoute('kiwicore_stock');
        }

        return $this->render('modules/eshop/stocks/show.html.twig', [
            'product' => $product,
            'transactions' => $doctrine->getRepository(ProductStockTransaction::class)->findProductHistory($product),
        ]);
    }
    
}


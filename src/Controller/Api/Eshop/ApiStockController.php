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

namespace App\Controller\Api\Eshop;

use App\Entity\Product;
use App\Entity\ProductStockTransaction;
use App\Service\ChartDataParser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Get stock information
 *
 * @author Simcao EI
 */
class ApiStockController extends AbstractController
{
    /**
     * Return JSON Response to draw charts of stocks
     *
     * @param ManagerRegistry $doctrine
     * @param int $id
     * @return Response
     */
    #[Route('/admin/api/eshop/stocks/transactions/{id}', name: 'kiwicore_api_eshop_stocks_transactions')]
    public function listStockTransactions(ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);
        $positiveTransactions = $doctrine->getRepository(ProductStockTransaction::class)->findPositiveTransactionGroupByMonthFormatted($product);
        $negativeTransactions = $doctrine->getRepository(ProductStockTransaction::class)->findNegativeTransactionGroupByMonthFormatted($product);

        $chartDataParser = new ChartDataParser();
        $chartDataParser->setLabels(array_keys($positiveTransactions));
        $chartDataParser->setDataset('Positif', $positiveTransactions);
        $chartDataParser->setDataset('Négatifs', $negativeTransactions);
        $parsedData = $chartDataParser->getParsedData();

        return new JsonResponse($parsedData);
    }
}

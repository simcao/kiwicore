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

/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection DuplicatedCode */

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductStockTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductStockTransaction>
 *
 * @method ProductStockTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductStockTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductStockTransaction[]    findAll()
 * @method ProductStockTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simcao EI
 */
class ProductStockTransactionRepository extends ServiceEntityRepository
{
    /**
     * Constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductStockTransaction::class);
    }

    /**
     * Save a transaction.
     *
     * @param ProductStockTransaction $entity
     * @param bool $flush
     * @return void
     */
    public function save(ProductStockTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a transaction.
     *
     * @param ProductStockTransaction $entity
     * @param bool $flush
     * @return void
     */
    public function remove(ProductStockTransaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Return array of transaction for a product order by date
     *
     * @param Product $product
     * @return array
     */
    public function findProductHistory(Product $product): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.product = :product')
            ->setParameter('product', $product)
            ->orderBy('p.transactionDate', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Return array of ProductTransaction object where quantity is positive for the current year
     *
     * @param Product $product
     * @return array
     */
    public function findPositiveTransactionGroupByMonth(Product $product): array
    {
        $configuration = $this->getEntityManager()->getConfiguration();
        $configuration->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $configuration->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $configuration->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        return $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.product = :product')
            ->andWhere('YEAR(t.transactionDate) = :currentYear')
            ->andWhere('t.quantity > 0')
            ->setParameter('product', $product)
            ->setParameter('currentYear', date('Y'))
            ->orderBy('t.transactionDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return formatted array positive transactions
     *
     * @param Product $product
     * @return int[]
     */
    public function findPositiveTransactionGroupByMonthFormatted(Product $product): array
    {
        $transactions = $this->findPositiveTransactionGroupByMonth($product);
        $results = $this->createMonthTableEmpty();

        foreach ($transactions as $transaction)
        {
            $key = (int) $transaction->getTransactionDate()->format('m');
            $key = $this->getMonthFromInt($key);
            $value = (int) $transaction->getQuantity();

            if(isset($results[$key]))
            {
                $results[$key] += $value;
            }
            else
            {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Return array of ProductTransaction object where quantity is negative for the current year
     *
     * @param Product $product
     * @return array
     */
    public function findNegativeTransactionGroupByMonth(Product $product): array
    {
        $configuration = $this->getEntityManager()->getConfiguration();
        $configuration->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $configuration->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $configuration->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        return $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.product = :product')
            ->andWhere('YEAR(t.transactionDate) = :currentYear')
            ->andWhere('t.quantity < 0')
            ->setParameter('product', $product)
            ->setParameter('currentYear', date('Y'))
            ->orderBy('t.transactionDate', 'ASC')
            ->getQuery()
            ->getResult();

    }

    /**
     * Return formatted array negative transactions
     *
     * @param Product $product
     * @return int[]
     */
    public function findNegativeTransactionGroupByMonthFormatted(Product $product): array
    {
        $transactions = $this->findNegativeTransactionGroupByMonth($product);
        $results = $this->createMonthTableEmpty();

        foreach ($transactions as $transaction)
        {
            $key = (int) $transaction->getTransactionDate()->format('m');
            $key = $this->getMonthFromInt($key);
            $value = (int) $transaction->getQuantity();

            if(isset($results[$key]))
            {
                $results[$key] += $value;
            }
            else
            {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Return month name as string based on month number
     *
     * @param int $month
     * @return string
     */
    private function getMonthFromInt(int $month): string
    {
        $months = [
          1 => 'Janvier',
          2 => 'Février',
          3 => 'Mars',
          4 => 'Avril',
          5 => 'Mai',
          6 => 'Juin',
          7 => 'Juillet',
          8 => 'Août',
          9 => 'Septembre',
          10 => 'Octobre',
          11 => 'Novembre',
          12 => 'Décembre'
        ];

        return $months[$month];
    }

    /**
     * Initialize empty array of month
     *
     * @return int[]
     */
    private function createMonthTableEmpty(): array
    {
        return [
            'Janvier' => 0,
            'Février' => 0,
            'Mars' => 0,
            'Avril' => 0,
            'Mai' => 0,
            'Juin' => 0,
            'Juillet' => 0,
            'Août' => 0,
            'Septembre' => 0,
            'Octobre' => 0,
            'Novembre' => 0,
            'Décembre' => 0,
        ];
    }
}

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

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simcao EI
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Save a product.
     *
     * @param Product $entity
     * @param bool $flush
     * @return void
     */
    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a product.
     *
     * @param Product $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Return array of all customers sorted by name
     *
     * @return array
     */
    public function findAllByName(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return Paginator[] of all customers sorted by name and paginated
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return Paginator
     */
    public function findAllByNamePaginated(int $currentPage = 1, int $pageSize = 16): Paginator
    {
        $dql = "SELECT p, pi, pc FROM App\Entity\Product p LEFT JOIN p.productImages pi LEFT JOIN p.category pc ORDER BY p.name ASC";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $paginator = new Paginator($query);

        $paginator->totalPages = ceil($paginator->count() / $pageSize);

        return $paginator;
    }

    public function findAllByStockPaginated(int $currentPage = 1, int $pageSize = 30): Paginator
    {
        $dql = "SELECT p FROM App\Entity\Product p WHERE p.stockable = 1 ORDER BY p.stock ASC, p.name ASC";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $paginator = new Paginator($query);

        $paginator->totalPages = ceil($paginator->count() / $pageSize);

        return $paginator;
    }
}

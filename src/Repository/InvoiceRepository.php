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

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simcao EI
 */
class InvoiceRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * Save a new invoice.
     *
     * @param Invoice $entity
     * @param bool $flush
     * @return void
     */
    public function save(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove invoice.
     *
     * @param Invoice $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Return Paginator[] of all invoices sorted by date and paginated
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return Paginator
     */
    public function findAllByDatePaginated(int $currentPage = 1, int $pageSize = 20): Paginator
    {
        $dql = "SELECT i, c FROM App\Entity\Invoice i LEFT JOIN i.customer c ORDER BY i.created_at DESC";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $paginator = new Paginator($query);

        $paginator->totalPages = ceil($paginator->count() / $pageSize);

        return $paginator;
    }

    /**
     * Return details of selected invoice
     *
     * @param $invoice
     * @return Invoice|null
     * @throws NonUniqueResultException
     */
    public function findOneWithDetails($invoice): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.id = :invoice')
            ->leftJoin('i.items', 'l')
            ->leftJoin('l.product', 'p')
            ->setParameter('invoice', $invoice)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

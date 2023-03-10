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

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Simcao EI
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Save a customer.
     *
     * @param Customer $entity
     * @param bool $flush
     * @return void
     */
    public function save(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a customer.
     *
     * @param Customer $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Customer $entity, bool $flush = false): void
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
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
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
    public function findAllByNamePaginated(int $currentPage = 1, int $pageSize = 12): Paginator
    {
        $dql = "SELECT c, cc FROM App\Entity\Customer c LEFT JOIN c.customerContacts cc ORDER BY c.name ASC";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setFirstResult(($currentPage - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $paginator = new Paginator($query);

        $paginator->totalPages = ceil($paginator->count() / $pageSize);

        return $paginator;
    }
}

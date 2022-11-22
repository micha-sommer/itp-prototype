<?php

namespace App\Repository;

use App\Entity\InvoicePosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoicePosition>
 *
 * @method InvoicePosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoicePosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoicePosition[]    findAll()
 * @method InvoicePosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoicePositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoicePosition::class);
    }

    public function save(InvoicePosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InvoicePosition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

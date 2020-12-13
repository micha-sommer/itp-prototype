<?php

namespace App\Repository;

use App\Entity\ChangeSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ChangeSet find($id, $lockMode = null, $lockVersion = null)
 * @method null|ChangeSet findOneBy(array $criteria, array $orderBy = null)
 * @method ChangeSet[]    findAll()
 * @method ChangeSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangeSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChangeSet::class);
    }

    /**
     * @throws \Exception
     *
     * @return ChangeSet[] Returns an array of ChangeSet objects
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if (null === $before) {
            $before = new \DateTime();
        }

        return $this->createQueryBuilder('c')
            ->andWhere('c.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('c.timestamp', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?ChangeSet
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

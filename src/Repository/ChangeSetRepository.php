<?php

namespace App\Repository;

use App\Entity\ChangeSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ChangeSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChangeSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChangeSet[]    findAll()
 * @method ChangeSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangeSetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChangeSet::class);
    }

    /**
     * @param \DateTimeInterface $after
     * @param \DateTimeInterface|null $before
     * @return ChangeSet[] Returns an array of ChangeSet objects
     * @throws \Exception
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if ($before === null) {
            $before = new \DateTime();
        }
        return $this->createQueryBuilder('c')
            ->andWhere('c.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('c.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
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

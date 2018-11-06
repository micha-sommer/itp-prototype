<?php

namespace App\Repository;

use App\Entity\Official;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Official|null find($id, $lockMode = null, $lockVersion = null)
 * @method Official|null findOneBy(array $criteria, array $orderBy = null)
 * @method Official[]    findAll()
 * @method Official[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficialsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Official::class);
    }


    /**
     * @param \DateTimeInterface $after
     * @param \DateTimeInterface|null $before
     * @return Official[] Returns an array of ChangeSet objects
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if ($before === null) {
            $before = new \DateTime();
        }
        return $this->createQueryBuilder('o')
            ->andWhere('o.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('o.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Official[] Returns an array of Official objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Official
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

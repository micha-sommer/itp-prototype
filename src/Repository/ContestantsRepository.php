<?php

namespace App\Repository;

use App\Entity\Contestant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contestant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contestant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contestant[]    findAll()
 * @method Contestant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestantsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contestant::class);
    }

    /**
     * @param \DateTimeInterface $after
     * @param \DateTimeInterface|null $before
     * @return Contestant[] Returns an array of ChangeSet objects
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

//    /**
//     * @return Contestant[] Returns an array of Contestant objects
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
    public function findOneBySomeField($value): ?Contestant
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

<?php

namespace App\Repository;

use App\Entity\Official;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Official find($id, $lockMode = null, $lockVersion = null)
 * @method null|Official findOneBy(array $criteria, array $orderBy = null)
 * @method Official[]    findAll()
 * @method Official[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Official::class);
    }

    /**
     * @throws \Exception
     *
     * @return Official[] Returns an array of ChangeSet objects
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if (null === $before) {
            $before = new \DateTime();
        }

        return $this->createQueryBuilder('o')
            ->andWhere('o.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('o.timestamp', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Official
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('o');

        foreach ($array as $id) {
            $query->orWhere('o.id = '.$id);
        }

        return $query->getQuery()
            ->getResult()
        ;
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

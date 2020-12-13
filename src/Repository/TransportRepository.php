<?php

namespace App\Repository;

use App\Entity\Transport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Transport find($id, $lockMode = null, $lockVersion = null)
 * @method null|Transport findOneBy(array $criteria, array $orderBy = null)
 * @method Transport[]    findAll()
 * @method Transport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transport::class);
    }

    /**
     * @throws \Exception
     *
     * @return Transport[] Returns an array of ChangeSet objects
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if (null === $before) {
            $before = new \DateTime();
        }

        return $this->createQueryBuilder('t')
            ->andWhere('t.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('t.timestamp', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Transport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('t');

        foreach ($array as $id) {
            $query->orWhere('t.id = '.$id);
        }

        return $query->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Transport[] Returns an array of Transport objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Registration::class);
    }

//    /**
//     * @return Registration[] Returns an array of Registration objects
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

    public function findOneByEmail($email): ?Registration
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneById($id): ?Registration
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

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

    /**
     * @param \DateTimeInterface $after
     * @param \DateTimeInterface|null $before
     * @return Registration[] Returns an array of Registrtion objects
     */
    public function findByDate(\DateTimeInterface $after, \DateTimeInterface $before = null): array
    {
        if ($before === null) {
            $before = new \DateTime();
        }
        return $this->createQueryBuilder('r')
            ->andWhere('r.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('r');

        foreach ($array as $id) {
            $query->orWhere('r.id = ' . $id);
        }

        return $query->getQuery()
            ->getResult();
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

    /**
     * @param $email
     * @return Registration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByEmail($email): ?Registration
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param $id
     * @return Registration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Registration
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Contestant;
use App\Entity\Registration;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method null|Registration find($id, $lockMode = null, $lockVersion = null)
 * @method null|Registration findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    /**
     * @throws Exception
     *
     * @return Registration[] Returns an array of Registrtion objects
     */
    public function findByDate(DateTimeInterface $after, DateTimeInterface $before = null): array
    {
        if (null === $before) {
            $before = new DateTime();
        }

        return $this->createQueryBuilder('r')
            ->andWhere('r.timestamp BETWEEN :from AND :to')
            ->setParameter('from', $after)
            ->setParameter('to', $before)
            ->orderBy('r.timestamp', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('r');

        foreach ($array as $id) {
            $query->orWhere('r.id = '.$id);
        }

        return $query->getQuery()
            ->getResult()
        ;
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
     *
     * @throws NonUniqueResultException
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

    /**
     * @param $id
     *
     * @throws NonUniqueResultException
     */
    public function findOneById($id): ?Registration
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findDistinctCountries(): ?array
    {
        $query = $this->createQueryBuilder('r');

        $result = $query
            ->select('r.country')
            ->distinct()
            ->join(Contestant::class, 'c', Join::WITH, 'r.id = c.registration')
            ->getQuery()
            ->getArrayResult()
        ;

        $result = array_map(static function ($a) {
            return $a['country'];
        }, $result);

        return $result;
    }
}

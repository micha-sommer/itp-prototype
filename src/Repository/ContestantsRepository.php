<?php

namespace App\Repository;

use App\Entity\Contestant;
use App\Enum\WeightCategoryEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Contestant find($id, $lockMode = null, $lockVersion = null)
 * @method null|Contestant findOneBy(array $criteria, array $orderBy = null)
 * @method Contestant[]    findAll()
 * @method Contestant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contestant::class);
    }

    /**
     * @throws \Exception
     *
     * @return Contestant[] Returns an array of ChangeSet objects
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

    /**
     * @param $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Contestant
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('c');

        foreach ($array as $id) {
            $query->orWhere('c.id = '.$id);
        }

        return $query->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCategory(string $age = null, string $weight = null): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
        ;
        if ($weight) {
            $query->where('c.weightCategory = :weight')
                ->setParameter('weight', $weight)
            ;
        } else {
            $query->where('c.weightCategory != :weight')
                ->setParameter('weight', WeightCategoryEnum::camp_only)
            ;
        }
        if ($age) {
            $query->andWhere('c.ageCategory = :age')
                ->setParameter('age', $age)
            ;
        }

        return $query->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCamp(string $age = null): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.itc != :itc')
            ->setParameter('itc', 'no')
        ;
        if ($age) {
            $query->andWhere('c.ageCategory = :age')
                ->setParameter('age', $age)
            ;
        }

        return $query->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Counts entities by a set of criteria.
     *
     * @param array|Criteria $criteria
     *
     * @return int the cardinality of the objects that match the given criteria
     */
    public function _count($criteria): int
    {
        return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
    }

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

<?php

namespace App\Repository;

use App\Entity\Contestant;
use App\Enum\WeightCategoryEnum;
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

    /**
     * @param $id
     * @return Contestant|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Contestant
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllById($array): array
    {
        $query = $this->createQueryBuilder('c');

        foreach ($array as $id) {
            $query->orWhere('c.id = ' . $id);
        }

        return $query->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $age
     * @param string|null $weight
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCategory(string $age = null, string $weight = null): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');
        if ($weight) {
            $query->where('c.weightCategory = :weight')
                ->setParameter('weight', $weight);
        } else {
            $query->where('c.weightCategory != :weight')
                ->setParameter('weight', WeightCategoryEnum::camp_only);
        }
        if ($age) {
            $query->andWhere('c.ageCategory = :age')
                ->setParameter('age', $age);
        }

        return $query->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string|null $age
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCamp(string $age = null): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.itc != :itc')
            ->setParameter('itc', 'no');
        if ($age) {
            $query->andWhere('c.ageCategory = :age')
                ->setParameter('age', $age);
        }

        return $query->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Counts entities by a set of criteria.
     *
     * @param  array|\Doctrine\Common\Collections\Criteria $criteria
     *
     * @return int The cardinality of the objects that match the given criteria.
     */
    public function _count($criteria) : int
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

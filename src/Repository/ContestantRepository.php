<?php

namespace App\Repository;

use App\Entity\Contestant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contestant>
 *
 * @method Contestant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contestant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contestant[]    findAll()
 * @method Contestant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contestant::class);
    }

    public function save(Contestant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contestant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

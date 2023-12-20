<?php

namespace App\Repository;

use App\Entity\Official;
use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Official>
 *
 * @method Official|null find($id, $lockMode = null, $lockVersion = null)
 * @method Official|null findOneBy(array $criteria, array $orderBy = null)
 * @method Official[]    findAll()
 * @method Official[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method int           count(array $array)
 */
class OfficialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Official::class);
    }

    public function save(Official $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Official $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAccommodationCount(): int
    {
        $criteria = Criteria::create()->where(Criteria::expr()->in('itcSelection', ['pack-A', 'pack-B', 'pack-C', 'pack-D']));

        return $this->countByCriteria($criteria);
    }

    private function countByCriteria(Criteria $criteria): int
    {
        return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
    }

    public function getPackACount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => 'pack-A']);
    }

    public function getPackBCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => 'pack-B']);
    }

    public function getPackCCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => 'pack-C']);
    }

    public function getPackDCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => 'pack-D']);
    }
}

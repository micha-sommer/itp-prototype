<?php

namespace App\Repository;

use App\Entity\Official;
use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function get1DayCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => '1-day']);
    }

    public function get2DaysCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => '2-day']);
    }

    public function get3DaysCount(?Registration $registration): int
    {
        return $this->count(['registration' => $registration, 'itcSelection' => '3-day']);
    }
}

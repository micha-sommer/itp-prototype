<?php

namespace App\Repository;

use App\Entity\Contestant;
use App\Entity\Registration;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
    private DateTimeImmutable $deadline;

    public function __construct(ManagerRegistry $registry, string $deadline)
    {
        parent::__construct($registry, Contestant::class);
        $this->deadline = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $deadline);
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

    public function getAccommodationCount(): int
    {
        $criteria = Criteria::create()->where(Criteria::expr()->in('itcSelection', ['pack-A', 'pack-B', 'pack-C', 'pack-D']));

        return $this->countByCriteria($criteria);
    }

    public function getContestantCount(): int
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('weightCategory', 'camp_only'));

        return $this->countByCriteria($criteria);
    }

    public function getRegularContestantCount(Registration $registration): int
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('registration', $registration))
            ->andWhere(Criteria::expr()->neq('weightCategory', 'camp_only'))
            ->andWhere(Criteria::expr()->lt('createdAt', $this->deadline));

        return $this->countByCriteria($criteria);
    }

    public function getLateContestantCount(Registration $registration): int
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('registration', $registration))
            ->andWhere(Criteria::expr()->neq('weightCategory', 'camp_only'))
            ->andWhere(Criteria::expr()->gte('createdAt', $this->deadline));

        return $this->countByCriteria($criteria);
    }

    public function countByCriteria(Criteria $criteria): int
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

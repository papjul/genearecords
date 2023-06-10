<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[] findAll()
 * @method Location[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function getCountries(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('l.country, SUM(l.all_records_total) as total')
            ->from(Location::class, 'l')
            ->groupBy('l.country')
            ->orderBy('l.country', 'ASC');
        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getProvinces(string $country)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('l.country, l.province, SUM(l.all_records_total) as total')
            ->from(Location::class, 'l')
            ->where('l.country = :country')
            ->groupBy('l.country, l.province')
            ->orderBy('l.province', 'ASC');
        $qb->setParameter('country', $country);
        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getPlaces(string $country, string $province)
    {
        return $this->findBy(['country' => $country, 'province' => $province], ['place' => 'ASC']);
    }
}

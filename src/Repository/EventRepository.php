<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[] findAll()
 * @method Event[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function getUniqueLocations()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('DISTINCT e.place, e.province, e.country')->from(Event::class, 'e');
        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function setStatistics(Location $location): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e.type, SUM(1) as total, MIN(e.year) AS min_year, MAX(e.year) as max_year')
            ->from(Event::class, 'e')
            ->groupBy('e.type');
        $sqlCount = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        $statistics = [
            'birth' => ['total' => 0, 'min_year' => null, 'max_year' => null],
            'baptism' => ['total' => 0, 'min_year' => null, 'max_year' => null],
            'marriage' => ['total' => 0, 'min_year' => null, 'max_year' => null],
            'death' => ['total' => 0, 'min_year' => null, 'max_year' => null],
            'burial' => ['total' => 0, 'min_year' => null, 'max_year' => null],
            'other' => ['total' => 0, 'min_year' => null, 'max_year' => null]
        ];
        $total = 0;
        $minYear = null;
        $maxYear = null;
        foreach ($sqlCount as $count) {
            $statistics[$count['type']]['total'] = $count['total'];
            $statistics[$count['type']]['min_year'] = $count['min_year'];
            $statistics[$count['type']]['max_year'] = $count['max_year'];
            $total += $count['total'];
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($statistics as $statisticType => $statistic) {
            $propertyAccessor->setValue($location, $statisticType.'_records_total', $statistic['total']);
            $propertyAccessor->setValue($location, $statisticType.'_records_year_from', $statistic['min_year']);
            $propertyAccessor->setValue($location, $statisticType.'_records_year_to', $statistic['max_year']);
            if ($statistic['min_year'] !== null && ($minYear === null || $minYear > $statistic['min_year'])) {
                $minYear = $statistic['min_year'];
            }
            if ($statistic['max_year'] !== null && ($maxYear === null || $maxYear < $statistic['max_year'])) {
                $maxYear = $statistic['max_year'];
            }
        }
        $location->setAllRecordsTotal($total);
        $location->setAllRecordsYearFrom($minYear);
        $location->setAllRecordsYearTo($maxYear);
    }
}

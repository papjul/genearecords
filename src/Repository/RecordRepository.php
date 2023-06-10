<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Import;
use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Record>
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    public function getFormChoices(): array
    {
        $fields = [];

        // Record fields
        $fields['record._'] = [
            'record.record_number' => 'record.record_number',
            'record.page_number' => 'record.page_number',
            'record.notes' => 'record.notes',
            'record.indexed_by' => 'record.indexed_by',
            'record.source' => 'record.source',
            'record.link' => 'record.link'
        ];

        // Person fields
        $personRelationships = ['individual', 'spouse', 'father', 'mother', 'father_father', 'father_mother', 'mother_father', 'mother_mother', 'spouse_father_father', 'spouse_father_mother', 'spouse_mother_father', 'spouse_mother_mother'];
        foreach($personRelationships as $personRelationship) {
            $fields['person.'.$personRelationship.'._'] = [
                'person.'.$personRelationship.'.surname' => 'person.'.$personRelationship.'.surname',
                'person.'.$personRelationship.'.given_names' => 'person.'.$personRelationship.'.given_names',
                'person.'.$personRelationship.'.age' => 'person.'.$personRelationship.'.age'
            ];
        }

        // Event fields
        $eventTypes = ['birth', 'baptism', 'marriage', 'death', 'burial', 'other'];
        foreach($eventTypes as $eventType) {
            $fields['event.'.$eventType.'._'] = [
                'event.'.$eventType.'.year' => 'event.'.$eventType.'.year',
                'event.'.$eventType.'.month' => 'event.'.$eventType.'.month',
                'event.'.$eventType.'.day' => 'event.'.$eventType.'.day',
                'event.'.$eventType.'.place' => 'event.'.$eventType.'.place',
                'event.'.$eventType.'.province' => 'event.'.$eventType.'.province',
                'event.'.$eventType.'.country' => 'event.'.$eventType.'.country'
            ];
        }
        $fields['event.other._'] = ['event.other.type' => 'event.other.type', ...$fields['event.other._']];

        return $fields;
    }

    public function deleteAllFromImport(Import $import, bool $persist = true) {
        $records = $this->findBy(['import' => $import]);

        foreach ($records as $record) {
            $this->getEntityManager()->remove($record);
        }

        if ($persist) {
            $this->getEntityManager()->flush();
        }
    }

    public function manualSearch(array $criterias)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')->from(Record::class, 'r');

        // Person
        if (isset($criterias['surname']) || isset($criterias['given_names'])) {
            $conditions = [];
            // TODO: Make it a parameter, or at least, sort by relevance
            $conditions[] = $qb->expr()->eq('p.relationship', ':individual');
            $qb->setParameter('individual', 'individual');
            if (isset($criterias['surname'])) {
                $conditions[] = $qb->expr()->like('p.surname', ':surname');
                $qb->setParameter('surname', '%'.$criterias['surname'].'%');
            }
            if (isset($criterias['given_names'])) {
                $conditions[] = $qb->expr()->like('p.given_names', ':given_names');
                $qb->setParameter('given_names', '%'.$criterias['given_names'].'%');
            }

            if (count($conditions)) {
                if (count($conditions) > 1) {
                    $andExpr = $qb->expr()->andX();
                    $andExpr->addMultiple($conditions);
                    $qb->join('r.persons', 'p', 'WITH', $andExpr);
                } else {
                    $qb->join('r.persons', 'p', 'WITH', $conditions[0]);
                }
            }
        }

        if (isset($criterias['year']) || isset($criterias['location'])) {
            $conditions = [];
            if (isset($criterias['year'])) {
                $years = explode('-', $criterias['year']);
                if (count($years) === 1 || count($years) === 2) {
                    $year = intval($years[0]);
                    if ($year >= 1000 && $year < 2100) {
                        if (count($years) === 1) {
                            $conditions[] = $qb->expr()->eq('e.year', ':year');
                            $qb->setParameter('year', $year);
                        }
                        if (count($years) === 2) {
                            $yearFrom = $year;
                            $yearTo = intval($years[1]);
                            if ($yearTo >= 1000 && $yearTo < 2100 && $yearFrom <= $yearTo) {
                                $conditions[] = $qb->expr()->gte('e.year', ':year_from');
                                $conditions[] = $qb->expr()->lte('e.year', ':year_to');
                                $qb->setParameter('year_from', $yearFrom);
                                $qb->setParameter('year_to', $yearTo);
                            }
                        }
                    }
                }
            }
            if (isset($criterias['location'])) {
                // TODO: Handle comma-separated locations (when user type "Milano, Italia" for example)

                $orExpr = $qb->expr()->orX();
                $orExpr->addMultiple([
                    $qb->expr()->like('e.place', ':location'),
                    $qb->expr()->like('e.province', ':location'),
                    $qb->expr()->like('e.country', ':location')
                ]);
                $conditions[] = $orExpr;
                $qb->setParameter('location', '%'.$criterias['location'].'%');
            }
            if (count($conditions)) {
                if (count($conditions) > 1) {
                    $andExpr = $qb->expr()->andX();
                    $andExpr->addMultiple($conditions);
                    $qb->join('r.events', 'e', 'WITH', $andExpr);
                } else {
                    $qb->join('r.events', 'e', 'WITH', $conditions[0]);
                }
            }
        }

        //$qb->orderBy('e.date', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

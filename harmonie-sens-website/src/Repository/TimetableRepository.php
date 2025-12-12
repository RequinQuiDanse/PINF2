<?php

namespace App\Repository;

use App\Entity\Timetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Timetable>
 */
class TimetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timetable::class);
    }

    public function findAvailableSlots(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isAvailable = :val')
            ->setParameter('val', true)
            ->orderBy('FIELD(t.dayOfWeek, \'lundi\', \'mardi\', \'mercredi\', \'jeudi\', \'vendredi\', \'samedi\', \'dimanche\')', 'ASC')
            ->addOrderBy('t.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

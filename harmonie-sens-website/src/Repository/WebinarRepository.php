<?php

namespace App\Repository;

use App\Entity\Webinar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Webinar>
 */
class WebinarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Webinar::class);
    }

    public function findUpcomingWebinars(): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.isActive = :active')
            ->andWhere('w.date >= :now')
            ->setParameter('active', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('w.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

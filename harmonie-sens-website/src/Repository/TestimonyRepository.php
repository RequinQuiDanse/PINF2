<?php

namespace App\Repository;

use App\Entity\Testimony;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Testimony>
 */
class TestimonyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimony::class);
    }

    public function findPublishedTestimonies(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isPublished = :val')
            ->setParameter('val', true)
            ->orderBy('t.displayOrder', 'ASC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

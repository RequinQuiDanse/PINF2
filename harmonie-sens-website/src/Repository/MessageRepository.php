<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findUnreadMessages(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isRead = :val')
            ->andWhere('m.isArchived = :archived')
            ->setParameter('val', false)
            ->setParameter('archived', false)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadMessages(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.isRead = :val')
            ->andWhere('m.isArchived = :archived')
            ->setParameter('val', false)
            ->setParameter('archived', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

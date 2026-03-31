<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function findByToken(string $token): ?Appointment
    {
        return $this->findOneBy(['token' => $token]);
    }

    public function findPendingAppointments(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', Appointment::STATUS_PENDING)
            ->orderBy('a.desiredDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countPendingAppointments(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.status = :status')
            ->setParameter('status', Appointment::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTakenTimeSlotsForDate(\DateTimeInterface $date): array
    {
        $results = $this->createQueryBuilder('a')
            ->select('a.desiredTime')
            ->where('a.desiredDate = :date')
            ->andWhere('a.status IN (:statuses)')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('statuses', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->getQuery()
            ->getSingleColumnResult();

        return $results;
    }

    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

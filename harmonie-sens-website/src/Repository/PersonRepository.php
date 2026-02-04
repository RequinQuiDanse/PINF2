<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * Recherche une personne par email
     */
    public function findOneByEmail(string $email): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche une personne par téléphone (si non null)
     */
    public function findOneByPhone(string $phone): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * Recherche des personnes par terme (nom, prénom, email, téléphone, organisation)
     */
    public function search(string $term): array
    {
        $qb = $this->createQueryBuilder('p');
        
        return $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(p.firstName)', ':term'),
                    $qb->expr()->like('LOWER(p.lastName)', ':term'),
                    $qb->expr()->like('LOWER(p.email)', ':term'),
                    $qb->expr()->like('p.phone', ':term'),
                    $qb->expr()->like('LOWER(p.organization)', ':term')
                )
            )
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSubscribedToNewsletter(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.subscribedToNewsletter = :val')
            ->setParameter('val', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

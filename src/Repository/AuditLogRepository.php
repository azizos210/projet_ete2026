<?php

namespace App\Repository;

use App\Entity\AuditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AuditLog> */
class AuditLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLog::class);
    }

    public function findByUtilisateur(int $userId, int $limit = 50): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.utilisateur = :user')
            ->setParameter('user', $userId)
            ->orderBy('a.dateAction', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByEntite(string $entite, int $entiteId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.entiteCible = :entite')
            ->andWhere('a.entiteId = :id')
            ->setParameter('entite', $entite)
            ->setParameter('id', $entiteId)
            ->orderBy('a.dateAction', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByPeriode(\DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.dateAction BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('a.dateAction', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByAction(string $action): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.action = :action')
            ->setParameter('action', $action)
            ->orderBy('a.dateAction', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

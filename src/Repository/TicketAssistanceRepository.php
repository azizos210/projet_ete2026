<?php

namespace App\Repository;

use App\Entity\TicketAssistance;
use App\Enum\StatutTicketEnum;
use App\Enum\PrioriteTicketEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<TicketAssistance> */
class TicketAssistanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketAssistance::class);
    }

    public function findOuverts(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.statut IN (:statuts)')
            ->setParameter('statuts', [StatutTicketEnum::OUVERT, StatutTicketEnum::EN_COURS])
            ->orderBy('t.priorite', 'DESC')
            ->addOrderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUrgents(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.priorite = :priorite')
            ->andWhere('t.statut != :resolu')
            ->setParameter('priorite', PrioriteTicketEnum::URGENTE)
            ->setParameter('resolu', StatutTicketEnum::RESOLU)
            ->orderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.demandeur = :patient')
            ->setParameter('patient', $patientId)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

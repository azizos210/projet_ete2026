<?php

namespace App\Repository;

use App\Entity\InformationAssurance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<InformationAssurance> */
class InformationAssuranceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationAssurance::class);
    }

    public function findByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.patient = :patient')
            ->setParameter('patient', $patientId)
            ->orderBy('i.compagnie', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

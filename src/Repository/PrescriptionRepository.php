<?php

namespace App\Repository;

use App\Entity\Prescription;
use App\Enum\StatutPrescriptionEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Prescription> */
class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }

    public function findActivesParPatient(int $patientId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.consultation', 'c')
            ->join('c.dossierMedical', 'd')
            ->where('d.patient = :patient')
            ->andWhere('p.statut = :statut')
            ->setParameter('patient', $patientId)
            ->setParameter('statut', StatutPrescriptionEnum::ACTIVE)
            ->orderBy('p.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\DossierMedical;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DossierMedical> */
class DossierMedicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierMedical::class);
    }

    public function findByPatientId(int $patientId): ?DossierMedical
    {
        return $this->createQueryBuilder('d')
            ->where('d.patient = :patient')
            ->setParameter('patient', $patientId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

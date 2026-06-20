<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Enum\StatutConsultationEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Consultation> */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function findByDossier(int $dossierId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.dossierMedical = :dossier')
            ->setParameter('dossier', $dossierId)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMedecinAndPeriode(int $medecinId, \DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.medecin = :medecin')
            ->andWhere('c.date BETWEEN :debut AND :fin')
            ->setParameter('medecin', $medecinId)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEnCours(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', StatutConsultationEnum::EN_COURS)
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

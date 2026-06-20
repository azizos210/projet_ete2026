<?php

namespace App\Repository;

use App\Entity\RendezVous;
use App\Enum\StatutRendezVousEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<RendezVous> */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    public function findByMedecinAndDate(int $medecinId, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.medecin = :medecin')
            ->andWhere('r.dateHeure >= :debut')
            ->andWhere('r.dateHeure < :fin')
            ->setParameter('medecin', $medecinId)
            ->setParameter('debut', $date->format('Y-m-d 00:00:00'))
            ->setParameter('fin', $date->format('Y-m-d 23:59:59'))
            ->orderBy('r.dateHeure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.patient = :patient')
            ->andWhere('r.dateHeure >= :now')
            ->andWhere('r.statut != :annule')
            ->setParameter('patient', $patientId)
            ->setParameter('now', new \DateTime())
            ->setParameter('annule', StatutRendezVousEnum::ANNULE)
            ->orderBy('r.dateHeure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findRappelsAEnvoyer(): array
    {
        $demain = new \DateTime('+24 hours');
        return $this->createQueryBuilder('r')
            ->where('r.dateHeure <= :demain')
            ->andWhere('r.dateHeure >= :maintenant')
            ->andWhere('r.rappelEnvoye = false')
            ->andWhere('r.statut = :confirme')
            ->setParameter('demain', $demain)
            ->setParameter('maintenant', new \DateTime())
            ->setParameter('confirme', StatutRendezVousEnum::CONFIRME)
            ->getQuery()
            ->getResult();
    }
}

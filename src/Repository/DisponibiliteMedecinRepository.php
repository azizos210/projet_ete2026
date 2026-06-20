<?php

namespace App\Repository;

use App\Entity\DisponibiliteMedecin;
use App\Enum\JourSemaineEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DisponibiliteMedecin> */
class DisponibiliteMedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisponibiliteMedecin::class);
    }

    public function findByMedecinAndJour(int $medecinId, JourSemaineEnum $jour): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.medecin = :medecin')
            ->andWhere('d.jourSemaine = :jour')
            ->setParameter('medecin', $medecinId)
            ->setParameter('jour', $jour)
            ->orderBy('d.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\AvisSpecialise;
use App\Enum\StatutAvisEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AvisSpecialise> */
class AvisSpecialiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvisSpecialise::class);
    }

    public function findEnAttenteParSpecialiste(int $medecinId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.medecinSpecialiste = :medecin')
            ->andWhere('a.statut = :statut')
            ->setParameter('medecin', $medecinId)
            ->setParameter('statut', StatutAvisEnum::EN_ATTENTE)
            ->orderBy('a.dateDemande', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

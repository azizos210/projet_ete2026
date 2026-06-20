<?php

namespace App\Repository;

use App\Entity\ProtocoleMedical;
use App\Enum\StatutProtocoleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProtocoleMedical> */
class ProtocoleMedicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProtocoleMedical::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.statut = :statut')
            ->setParameter('statut', StatutProtocoleEnum::ACTIF)
            ->orderBy('p.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

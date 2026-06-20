<?php

namespace App\Repository;

use App\Entity\AdministrationMedicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AdministrationMedicament> */
class AdministrationMedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdministrationMedicament::class);
    }

    public function findContreIndicationsSignalees(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.contreIndicationSignalee = true')
            ->orderBy('a.dateHeure', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

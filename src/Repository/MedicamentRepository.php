<?php

namespace App\Repository;

use App\Entity\Medicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Medicament> */
class MedicamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicament::class);
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.nom LIKE :q OR m.formePharmaceutique LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

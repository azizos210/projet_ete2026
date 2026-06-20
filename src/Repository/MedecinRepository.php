<?php

namespace App\Repository;

use App\Entity\Medecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Medecin> */
class MedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medecin::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('m.specialite', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySpecialite(string $specialite): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.specialite LIKE :s')
            ->setParameter('s', '%' . $specialite . '%')
            ->andWhere('m.actif = true')
            ->getQuery()
            ->getResult();
    }
}

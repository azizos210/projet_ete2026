<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Patient> */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    public function searchByNom(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.utilisateur', 'u')
            ->where('u.nom LIKE :q OR u.prenom LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

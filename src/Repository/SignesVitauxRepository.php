<?php

namespace App\Repository;

use App\Entity\SignesVitaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SignesVitaux> */
class SignesVitauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SignesVitaux::class);
    }

    public function findAlertesByConsultation(int $consultationId): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.consultation = :consultation')
            ->andWhere('s.alerteDeclenchee = true')
            ->setParameter('consultation', $consultationId)
            ->orderBy('s.dateMesure', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

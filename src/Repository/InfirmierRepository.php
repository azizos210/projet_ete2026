<?php

namespace App\Repository;

use App\Entity\Infirmier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Infirmier> */
class InfirmierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infirmier::class);
    }

    public function findByService(string $service): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.service = :service')
            ->setParameter('service', $service)
            ->getQuery()
            ->getResult();
    }
}

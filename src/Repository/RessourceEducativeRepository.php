<?php

namespace App\Repository;

use App\Entity\RessourceEducative;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<RessourceEducative> */
class RessourceEducativeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RessourceEducative::class);
    }

    public function findByCategorie(string $categorie): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.categorie = :categorie')
            ->setParameter('categorie', $categorie)
            ->orderBy('r.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

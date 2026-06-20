<?php

namespace App\Repository;

use App\Entity\Paiement;
use App\Enum\MethodePaiementEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Paiement> */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function findByFacture(int $factureId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.facture = :facture')
            ->setParameter('facture', $factureId)
            ->orderBy('p.dateTransaction', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMethode(MethodePaiementEnum $methode): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.methode = :methode')
            ->setParameter('methode', $methode)
            ->orderBy('p.dateTransaction', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Enum\StatutFactureEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Facture> */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function findImpayeesParPatient(int $patientId): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.patient = :patient')
            ->andWhere('f.statutPaiement != :payee')
            ->setParameter('patient', $patientId)
            ->setParameter('payee', StatutFactureEnum::PAYEE)
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByNumero(string $numero): ?Facture
    {
        return $this->findOneBy(['numero' => $numero]);
    }

    public function getTotalEncaisseParPeriode(\DateTimeInterface $debut, \DateTimeInterface $fin): float
    {
        $result = $this->createQueryBuilder('f')
            ->select('SUM(f.montant) as total')
            ->where('f.dateEmission BETWEEN :debut AND :fin')
            ->andWhere('f.statutPaiement = :payee')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setParameter('payee', StatutFactureEnum::PAYEE)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}

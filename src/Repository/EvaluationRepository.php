<?php

namespace App\Repository;

use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Evaluation> */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function getNoteMoyenneParMedecin(int $medecinId): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('AVG(e.note) as moyenne')
            ->join('e.consultation', 'c')
            ->where('c.medecin = :medecin')
            ->setParameter('medecin', $medecinId)
            ->getQuery()
            ->getSingleScalarResult();

        return round((float) ($result ?? 0), 2);
    }

    public function findByPatient(int $patientId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.patient = :patient')
            ->setParameter('patient', $patientId)
            ->orderBy('e.dateEvaluation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\DocumentMedical;
use App\Enum\TypeDocumentEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DocumentMedical> */
class DocumentMedicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentMedical::class);
    }

    public function findByDossierAndType(int $dossierId, TypeDocumentEnum $type): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.dossierMedical = :dossier')
            ->andWhere('d.type = :type')
            ->setParameter('dossier', $dossierId)
            ->setParameter('type', $type)
            ->orderBy('d.dateUpload', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

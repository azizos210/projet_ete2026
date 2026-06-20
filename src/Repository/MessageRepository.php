<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Message> */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findConversation(int $userId1, int $userId2): array
    {
        return $this->createQueryBuilder('m')
            ->where(
                '(m.expediteur = :u1 AND m.destinataire = :u2) OR (m.expediteur = :u2 AND m.destinataire = :u1)'
            )
            ->setParameter('u1', $userId1)
            ->setParameter('u2', $userId2)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countNonLusByDestinataire(int $userId): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.destinataire = :user')
            ->andWhere('m.lu = false')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

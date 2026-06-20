<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Enum\TypeNotificationEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Notification> */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findNonLuesByUser(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.destinataire = :user')
            ->andWhere('n.lu = false')
            ->setParameter('user', $userId)
            ->orderBy('n.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(int $userId, TypeNotificationEnum $type): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.destinataire = :user')
            ->andWhere('n.type = :type')
            ->setParameter('user', $userId)
            ->setParameter('type', $type)
            ->orderBy('n.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function marquerToutesLues(int $userId): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.lu', true)
            ->where('n.destinataire = :user')
            ->andWhere('n.lu = false')
            ->setParameter('user', $userId)
            ->getQuery()
            ->execute();
    }
}

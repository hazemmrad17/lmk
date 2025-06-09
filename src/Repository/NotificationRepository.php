<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find notifications by user
     */
    public function findByUser(int $userId): array
    {
        return $this->findBy(['user' => $userId]);
    }

    /**
     * Find notifications by type
     */
    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    /**
     * Find unread notifications
     */
    public function findUnread(): array
    {
        return $this->findBy(['isRead' => false]);
    }

    /**
     * Find notifications by date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.createdAt >= :startDate')
            ->andWhere('n.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', true)
            ->where('n.user = :userId')
            ->andWhere('n.isRead = false')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
} 
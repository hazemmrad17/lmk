<?php

namespace App\Repository;

use App\Entity\AvailabilitySlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvailabilitySlot>
 *
 * @method AvailabilitySlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method AvailabilitySlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method AvailabilitySlot[]    findAll()
 * @method AvailabilitySlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailabilitySlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvailabilitySlot::class);
    }

    public function save(AvailabilitySlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AvailabilitySlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find slots by tutor
     */
    public function findByTutor(int $tutorId): array
    {
        return $this->findBy(['tutor' => $tutorId]);
    }

    /**
     * Find slots by day of week
     */
    public function findByDayOfWeek(string $dayOfWeek): array
    {
        return $this->findBy(['dayOfWeek' => $dayOfWeek]);
    }

    /**
     * Find slots by time range
     */
    public function findByTimeRange(\DateTimeInterface $startTime, \DateTimeInterface $endTime): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.startTime >= :startTime')
            ->andWhere('a.endTime <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find available slots for a tutor on a specific day
     */
    public function findAvailableSlotsForDay(int $tutorId, string $dayOfWeek): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.tutor = :tutorId')
            ->andWhere('a.dayOfWeek = :dayOfWeek')
            ->setParameter('tutorId', $tutorId)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->orderBy('a.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 
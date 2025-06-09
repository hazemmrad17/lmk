<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find bookings by student
     */
    public function findByStudent(int $studentId): array
    {
        return $this->findBy(['student' => $studentId]);
    }

    /**
     * Find bookings by tutor
     */
    public function findByTutor(int $tutorId): array
    {
        return $this->findBy(['tutor' => $tutorId]);
    }

    /**
     * Find bookings by status
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Find bookings by date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.startTime >= :startDate')
            ->andWhere('b.endTime <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming bookings
     */
    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.startTime > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('b.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 
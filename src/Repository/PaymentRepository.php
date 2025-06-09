<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find payment by booking
     */
    public function findByBooking(int $bookingId): ?Payment
    {
        return $this->findOneBy(['booking' => $bookingId]);
    }

    /**
     * Find payments by status
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Find payments by amount range
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.amount >= :minAmount')
            ->andWhere('p.amount <= :maxAmount')
            ->setParameter('minAmount', $minAmount)
            ->setParameter('maxAmount', $maxAmount)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find payments by date range
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.createdAt >= :startDate')
            ->andWhere('p.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }
} 
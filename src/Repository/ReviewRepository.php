<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find reviews by tutor
     */
    public function findByTutor(int $tutorId): array
    {
        return $this->findBy(['tutor' => $tutorId]);
    }

    /**
     * Find reviews by student
     */
    public function findByStudent(int $studentId): array
    {
        return $this->findBy(['student' => $studentId]);
    }

    /**
     * Find reviews by rating range
     */
    public function findByRatingRange(float $minRating, float $maxRating): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rating >= :minRating')
            ->andWhere('r.rating <= :maxRating')
            ->setParameter('minRating', $minRating)
            ->setParameter('maxRating', $maxRating)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get average rating for a tutor
     */
    public function getAverageRatingForTutor(int $tutorId): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as averageRating')
            ->andWhere('r.tutor = :tutorId')
            ->setParameter('tutorId', $tutorId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
} 
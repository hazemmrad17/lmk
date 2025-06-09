<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 *
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function save(Course $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Course $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a course by slug
     */
    public function findBySlug(string $slug): ?Course
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Find courses by tutor
     */
    public function findByTutor(int $tutorId): array
    {
        return $this->findBy(['tutor' => $tutorId]);
    }

    /**
     * Find courses by subject
     */
    public function findBySubject(int $subjectId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.subjects', 's')
            ->andWhere('s.id = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active courses
     */
    public function findActive(): array
    {
        return $this->findBy(['isActive' => true]);
    }

    /**
     * Find courses by price range
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.price >= :minPrice')
            ->andWhere('c.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->getQuery()
            ->getResult();
    }
} 
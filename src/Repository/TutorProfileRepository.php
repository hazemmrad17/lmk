<?php

namespace App\Repository;

use App\Entity\TutorProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TutorProfile>
 *
 * @method TutorProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TutorProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TutorProfile[]    findAll()
 * @method TutorProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutorProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TutorProfile::class);
    }

    public function save(TutorProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TutorProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find tutors by subject
     */
    public function findBySubject(int $subjectId): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.subjects', 's')
            ->andWhere('s.id = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tutors by hourly rate range
     */
    public function findByHourlyRateRange(float $minRate, float $maxRate): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.hourlyRate >= :minRate')
            ->andWhere('t.hourlyRate <= :maxRate')
            ->setParameter('minRate', $minRate)
            ->setParameter('maxRate', $maxRate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tutors with availability
     */
    public function findWithAvailability(): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.availabilitySlots', 'a')
            ->getQuery()
            ->getResult();
    }
} 
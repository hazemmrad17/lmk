<?php

namespace App\Repository;

use App\Entity\StudentProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudentProfile>
 *
 * @method StudentProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentProfile[]    findAll()
 * @method StudentProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentProfile::class);
    }

    public function save(StudentProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StudentProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find students by grade level
     */
    public function findByGradeLevel(string $gradeLevel): array
    {
        return $this->findBy(['gradeLevel' => $gradeLevel]);
    }

    /**
     * Find students by trial status
     */
    public function findByTrialStatus(string $status): array
    {
        return $this->findBy(['trialStatus' => $status]);
    }

    /**
     * Find students with parent email
     */
    public function findWithParentEmail(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.parentEmail IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
} 
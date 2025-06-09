<?php

namespace App\Repository;

use App\Entity\FileMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FileMetadata>
 *
 * @method FileMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileMetadata[]    findAll()
 * @method FileMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileMetadata::class);
    }

    public function save(FileMetadata $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FileMetadata $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find files by type
     */
    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    /**
     * Find files by mime type
     */
    public function findByMimeType(string $mimeType): array
    {
        return $this->findBy(['mimeType' => $mimeType]);
    }

    /**
     * Find files by size range
     */
    public function findBySizeRange(int $minSize, int $maxSize): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.size >= :minSize')
            ->andWhere('f.size <= :maxSize')
            ->setParameter('minSize', $minSize)
            ->setParameter('maxSize', $maxSize)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find files by uploader
     */
    public function findByUploader(int $userId): array
    {
        return $this->findBy(['uploadedBy' => $userId]);
    }
} 
<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tutorial>
 */
class TutorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorial::class);
    }

    public function findByFilters(?string $search, ?Category $category): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->leftJoin('t.category', 'c')
            ->addSelect('c')
            ->leftJoin('t.auteur', 'a')
            ->addSelect('a')
            ->orderBy('t.createdAt', 'DESC');

        if ($search !== null && $search !== '') {
            $queryBuilder
                ->andWhere('LOWER(t.title) LIKE :search OR LOWER(t.content) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        if ($category !== null) {
            $queryBuilder
                ->andWhere('t.category = :category')
                ->setParameter('category', $category);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
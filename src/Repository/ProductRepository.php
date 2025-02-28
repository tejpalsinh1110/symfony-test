<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function createSortedQueryBuilder(?string $sortBy = 'title', ?string $order = 'ASC'): QueryBuilder
    {
        $validFields = ['title', 'priceExclVat', 'category'];
        $sortBy = in_array($sortBy, $validFields) ? $sortBy : 'title';
        $order = 'DESC' === strtoupper((string) $order) ? 'DESC' : 'ASC';

        return $this->createQueryBuilder('p')
            ->orderBy('p.' . $sortBy, $order);
    }

    /**
     * @return array{0: Product[], 1: int}
     */
    public function findAllWithPagination(int $page, int $limit, ?string $sortBy = 'title', ?string $order = 'ASC', ?string $category = null): array
    {
        $qb = $this->createSortedQueryBuilder($sortBy, $order);

        if ($category) {
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $category);
        }

        $total = count($qb->getQuery()->getResult());

        $products = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [$products, $total];
    }
}

<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\ProductFilter;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getById(int $id): Product
    {
        $product = $this->findOneBy(['id' => $id]);

        return $product ?? throw new NotFound();
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function delete(Product $product): void
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }

    public function findByFilter(ProductFilter $filter, ?int $userId = null): array
    {
        $qb = (new RequestQueryBuilder($this->getEntityManager()))
            ->createQueryBuilder($this->getClassName(), $filter);

        if ($userId) {
            $qb->andWhere('t.userId = :userId')
                ->setParameter('userId', $userId);
        }

        $qb->andWhere("LOWER(t.title) LIKE :query")
            ->setParameter('query', '%' . strtolower($filter->getQuery()) . '%');

        return $qb->getQuery()->getResult();
    }
}


<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;


use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Application\DTO\QueryFilter;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    public function findByFilter(QueryFilter $filter): array
    {
        $qb = (new RequestQueryBuilder($this->getEntityManager()))
            ->createQueryBuilder($this->getClassName(), $filter);


        return $qb->getQuery()->getResult();
    }
}

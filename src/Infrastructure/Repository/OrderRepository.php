<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\OrderProduct;
use BackendTestApp\Infrastructure\Repository\OrderProductRepository;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);

    }

    public function getById(int $id)
    {
        $order = $this->findOneBy(['id' => $id]);
        return $order ?? throw new NotFound();
    }

    public function findByFilter(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o');

        return $qb->getQuery()->getResult();
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function delete(Order $order): void
    {
        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();
    }
}

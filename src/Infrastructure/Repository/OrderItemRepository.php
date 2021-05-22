<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\OrderItemFilter;
use BackendTestApp\Domain\Entity\OrderItem;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Exception\BackendTestAppException;
use BackendTestApp\Domain\Exception\NotFound;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function getById(int $id): OrderItem
    {
        $orderItem = $this->findOneBy(['id' => $id]);

        return $orderItem ?? throw new NotFound();
    }




    public function save(OrderItem $orderItem): void
    {
        $this->getEntityManager()->persist($orderItem);
        $this->getEntityManager()->flush();
    }

    public function delete(OrderItem $orderItem): void
    {
        $this->getEntityManager()->remove($orderItem);
        $this->getEntityManager()->flush();
    }


}

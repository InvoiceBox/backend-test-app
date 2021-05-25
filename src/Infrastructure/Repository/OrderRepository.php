<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\OrderFilter;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getById(int $id): Order
    {
        $order = $this->findOneBy(['id' => $id]);

        return $order ?? throw new NotFound();
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

    public function findByFilter(OrderFilter $filter, ?int $userId = null): array
    {
        $qb = (new RequestQueryBuilder($this->getEntityManager()))
            ->createQueryBuilder($this->getClassName(), $filter);

        if ($userId) {
            $qb->andWhere('t.userId = :userId')
                ->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }
}


<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\Example;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Example::class);
    }

    public function getById(int $id): Example
    {
        $example = $this->findOneBy(['id' => $id]);

        return $example ?? throw new NotFound();
    }

    public function save(Example $example): void
    {
        $this->getEntityManager()->persist($example);
        $this->getEntityManager()->flush();
    }

    public function delete(Example $example): void
    {
        $this->getEntityManager()->remove($example);
        $this->getEntityManager()->flush();
    }

    public function findByFilter(ExampleFilter $filter, ?int $userId = null): array
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


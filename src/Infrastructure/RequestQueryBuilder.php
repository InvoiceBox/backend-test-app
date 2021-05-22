<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure;

use BackendTestApp\Application\DTO\QueryFilter;
use BackendTestApp\Domain\Exception\WrongFilterFormat;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RequestQueryBuilder
{
    public const ORDER_MAP = ['ASC', 'DESC'];

    protected QueryBuilder $queryBuilder;
    protected ClassMetadata $metaData;
    protected EntityManagerInterface $em;
    protected int $counter = 0;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @throws WrongFilterFormat
     */
    public function createQueryBuilder(string $class, QueryFilter $filter): QueryBuilder
    {
        $this->counter = 0;
        $this->metaData = $this->em->getMetadataFactory()->getMetadataFor($class);
        $this->queryBuilder = $this->em
            ->createQueryBuilder()
            ->select('t')
            ->from($class, 't');
        $this->processLimitOffset($filter->getPageSize(), $filter->getPage());
        $this->processOrders($filter->getOrder());

        return $this->queryBuilder;
    }

    /**
     * @throws WrongFilterFormat
     */
    protected function processLimitOffset(int $pageSize, int $page): void
    {
        if ($pageSize < 1) {
            throw new WrongFilterFormat('limit parse error');
        }

        if ($page === 0 || $page < 0) {
            throw new WrongFilterFormat('offset parse error');
        }

        $this->queryBuilder
            ->setMaxResults($pageSize)
            ->setFirstResult($pageSize * ($page - 1));
    }

    private function processOrders(array $orders): void
    {
        foreach ($orders as $fieldName => $fieldValue) {
            if ($this->metaData->hasField($fieldName)) {
                $fieldValue = strtoupper($fieldValue);
                if (in_array($fieldValue, static::ORDER_MAP, true)) {
                    $this->queryBuilder->addOrderBy('t.' . $fieldName, $fieldValue);
                }
            }
        }
    }
}

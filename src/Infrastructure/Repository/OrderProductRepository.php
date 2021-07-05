<?php


namespace BackendTestApp\Infrastructure\Repository;


use BackendTestApp\Domain\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\Example;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function fined(ExampleFilter $filter, int $userId)
    {
        $fields = [
            'o.id',
            'p.title',
        ];

        return (new RequestQueryBuilder($this->getEntityManager()))
            ->createQueryBuilder($this->getClassName(), $filter)
            ->select($fields)
            ->join('t.orderId', 'o')
            ->join('t.productId', 'p')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('o.id')
            ->getQuery()
            ->getResult();

    }

}
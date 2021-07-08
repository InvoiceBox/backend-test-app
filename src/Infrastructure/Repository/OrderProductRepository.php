<?php


namespace BackendTestApp\Infrastructure\Repository;


use BackendTestApp\Application\DTO\QueryFilter;
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

    public function findByFilterAndUserId(QueryFilter $filter, int $userId, int $orderId)
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
            ->andWhere('o.id = :orderId')
            ->setParameter('userId', $userId)
            ->setParameter('orderId', $orderId)
            ->orderBy('o.id')
            ->getQuery()
            ->getResult();
    }

    public function remove(OrderProduct $orderProduct){
        $this->getEntityManager()->remove($orderProduct);
        $this->getEntityManager()->flush();
    }

    public function save(OrderProduct $orderProduct)
    {
        $this->getEntityManager()->persist($orderProduct);
        $this->getEntityManager()->flush();
    }
}
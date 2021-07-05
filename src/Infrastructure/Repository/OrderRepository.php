<?php

declare(strict_types=1);

namespace BackendTestApp\Infrastructure\Repository;

use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\OrderProduct;
use BackendTestApp\Infrastructure\Repository\OrderProductRepository;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Entity\Orders;
use BackendTestApp\Domain\Exception\NotFound;
use BackendTestApp\Infrastructure\RequestQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private OrderProductRepository $orderProductRepository
    , private ProductRepository $productRepository)
    {
        parent::__construct($registry, Orders::class);

    }

    public function getById(int $id)
    {
        $order = $this->findOneBy(['id' => $id]);
        return $order ?? throw new NotFound();
    }

    public function save(Orders $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function delete(Orders $order): void
    {
        $this->getEntityManager()->remove($order);
        $this->getEntityManager()->flush();
    }

    public function findByFilter(ExampleFilter $filter, ?int $userId = null)
    {
        return $this->orderProductRepository->fined($filter, $userId);
    }

    public function insertProduct(Orders $orders, Product $product) : void
    {
        $product->updateRemoveCountAndPriceForAll();
        $orderProduct = new OrderProduct();
        $orderProduct->setOrderId($orders);
        $orderProduct->setProductId($product);
        $this->getEntityManager()->persist($orderProduct);
        $this->getEntityManager()->flush();

        $this->refreshAmount($orders);
    }

    public function removeProduct(Orders $orders, Product $product) : void
    {
        $product->updateAddCountAndPriceForAll();
        $this->getEntityManager()->remove($this->orderProductRepository->findOneBy(['orderId' => $orders, 'productId' => $product]));
        $this->getEntityManager()->flush();

        $this->refreshAmount($orders);
    }

    public function refreshAmount(Orders $orders)
    {
        $amount = 0;
        foreach ($this->orderProductRepository->findBy(['orderId' => $orders]) as $record)
        {
            $amount += $record->getProductId()->getPrice();
        }
        $orders->setAmount($amount);
        $this->getEntityManager()->flush();
    }
}

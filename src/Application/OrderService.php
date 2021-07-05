<?php


namespace BackendTestApp\Application;


use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\Orders;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Infrastructure\Repository\OrderRepository;
use Psr\Log\LoggerInterface;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function findByFilter(ExampleFilter $filter, ?int $userId = null): array
    {
        return $this->orderRepository->findByFilter($filter, $userId);
    }

    public function getById(int $id): Orders
    {
        return $this->orderRepository->getById($id);
    }

    public function create(Orders $order, ?int $userId = null): void
    {
        if ($userId) {
            $order->setUserId($userId);
        }

        $this->orderRepository->save($order);

        $this->logger->info('order entity has been created', ['id' => $order->getId()]);
    }

    public function update(Orders $order): void
    {
        $this->orderRepository->save($order);
    }

    public function delete(Orders $order): void
    {
        $this->orderRepository->delete($order);
    }

    public function insertProduct(Orders $orders, Product $product)
    {
        $this->orderRepository->insertProduct($orders, $product);
    }

    public function removeProduct(Orders $orders, Product $product)
    {
        $this->orderRepository->removeProduct($orders, $product);
    }
}
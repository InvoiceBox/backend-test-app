<?php


namespace BackendTestApp\Application;


use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Application\DTO\QueryFilter;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Domain\Entity\OrderProduct;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Infrastructure\Repository\OrderProductRepository;
use BackendTestApp\Infrastructure\Repository\OrderRepository;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class OrderService
{

    public function __construct(
        private OrderRepository $orderRepository,
        private LoggerInterface $logger,
        private ProductService $productService,
        private OrderProductRepository $orderProductRepository,
    ) {
    }

    public function findByFilter(QueryFilter $filter, ?int $userId = null, int $orderId = null): array
    {
        if ($orderId != null){
            return $this->orderProductRepository->findByFilterAndUserId($filter, $userId, $orderId);
        }
        return $this->orderRepository->findByFilter();
    }

    public function getById(int $id): Order
    {
        return $this->orderRepository->getById($id);
    }

    public function create(Order $order, ?int $userId = null): void
    {
        if ($userId) {
            $order->setUserId($userId);
        }

        $this->orderRepository->save($order);

        $this->logger->info('order entity has been created', ['id' => $order->getId()]);
    }

    public function update(Order $order): void
    {
        $this->orderRepository->save($order);
    }

    public function delete(Order $order): void
    {
        $this->orderRepository->delete($order);
    }

    public function addProduct(Order $orders, Product $product)
    {
        $this->productService->updateRemoveCountAndPriceForAll($product);
        $orderProduct = new OrderProduct();
        $orderProduct->setOrderId($orders);
        $orderProduct->setProductId($product);
        $this->orderProductRepository->save($orderProduct);

        $this->refreshAmount($orders);
    }

    public function removeProduct(Order $orders, Product $product)
    {
        $this->productService->updateAddCountAndPriceForAll($product);
        $this->orderProductRepository->remove($this->orderProductRepository->findOneBy(['orderId' => $orders, 'productId' => $product]));


        $this->refreshAmount($orders);
    }

    private function refreshAmount(Order $orders)
    {
        $amount = 0;
        foreach ($this->orderProductRepository->findBy(['orderId' => $orders]) as $record)
        {
            $amount += $record->getProductId()->getPrice();
        }
        $orders->setAmount($amount);

        $this->orderRepository->save($orders);
    }
}
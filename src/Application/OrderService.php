<?php

declare(strict_types=1);

namespace BackendTestApp\Application;

use BackendTestApp\Application\DTO\OrderFilter;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Domain\Entity\OrderItem;
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

    public function findByFilter(OrderFilter $filter, ?int $userId = null): array
    {
        return $this->orderRepository->findByFilter($filter, $userId);
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

        /**
         * Здесь может быть любая бизнес логика:
         * -----
         * $foo = $this->fooService->getById($example->getFooId());
         * $example->setFoo($foo);
         * -----
         * $this->eventDispatcher->dispatch(new OnExampleCreatedEvent($example));
         */

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


    /**
     * @param \BackendTestApp\Domain\Entity\Order $order
     * @param Product $product
     */
    public function addProduct(Order $order, Product $product)
    {
        $item = new OrderItem();
        $item->setProduct($product);
        $order->addItem($item);
        $this->update($order);
    }

    /**
     * @param \BackendTestApp\Domain\Entity\Order $order
     * @param int $productId
     */
    public function deleteProduct(Order $order, int $productId)
    {
        $item = $order->getItemByProductId($productId);
        $order->deleteItem($item);
        $this->update($order);
    }

    /**
     *
     */
    public function deleteItems(Order $order)
    {
        $order->deleteAllItems();
        $this->update($order);
    }

    /**
     *
     */
    public function deleteItem(Order $order, int $itemId)
    {
        $item = $order->getItemById($itemId);
        $order->deleteItem($item);
        $this->update($order);
    }
}


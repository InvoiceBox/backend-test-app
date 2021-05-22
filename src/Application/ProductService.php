<?php

declare(strict_types=1);

namespace BackendTestApp\Application;

use BackendTestApp\Application\DTO\ProductFilter;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Infrastructure\Repository\ProductRepository;
use Psr\Log\LoggerInterface;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function findByFilter(ProductFilter $filter, ?int $userId = null): array
    {
        return $this->productRepository->findByFilter($filter, $userId);
    }

    public function getById(int $id): Product
    {
        return $this->productRepository->getById($id);
    }

    public function create(Product $product, ?int $userId = null): void
    {
        if ($userId) {
            $product->setUserId($userId);
        }

        /**
         * Здесь может быть любая бизнес логика:
         * -----
         * $foo = $this->fooService->getById($example->getFooId());
         * $example->setFoo($foo);
         * -----
         * $this->eventDispatcher->dispatch(new OnExampleCreatedEvent($example));
         */

        $this->productRepository->save($product);

        $this->logger->info('product entity has been created', ['id' => $product->getId()]);
    }

    public function update(Product $product): void
    {
        $this->productRepository->save($product);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}

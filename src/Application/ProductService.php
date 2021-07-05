<?php


namespace BackendTestApp\Application;


use BackendTestApp\Application\DTO\ExampleFilter;
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

    public function findByFilter(ExampleFilter $filter, ?int $userId = null): array
    {
        return $this->productRepository->findByFilter($filter, $userId);
    }

    public function getById(int $id): Product
    {
        return $this->productRepository->getById($id);
    }

    public function create(Product $product): void
    {

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
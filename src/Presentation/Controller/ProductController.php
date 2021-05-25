<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Controller;

use BackendTestApp\Application\ProductService;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Application\DTO\ProductFilter;
use BackendTestApp\Presentation\AuthenticationManager;
use BackendTestApp\Presentation\Serializer\JsonResponse;
use BackendTestApp\Presentation\Serializer\JsonSerializer;
use BackendTestApp\Presentation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController
{
    public function __construct(
        private ProductService $productService,
        private JsonSerializer $serializer,
        private Validator $validator,
        private AuthenticationManager $privateAuthenticationManager
    )
    {
    }

    #[Route('/product', name: 'find_product', methods: ['GET'])]
    public function find(
        Request $request,
        array $readSerializationGroups
    ): JsonResponse
    {

        $filter = new ProductFilter($request->query->all());

        $product = $this->productService->findByFilter($filter, $this->privateAuthenticationManager->getCurrentUserId());

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }

    #[Route('/product/{id}', name: 'get_product', methods: ['GET'])]
    public function get(
        int $id,
        array $readSerializationGroups
    ): JsonResponse
    {
        $product = $this->productService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($product->getUserId());

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }

    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse
    {
        $product = $this->productService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($product->getUserId());

        $this->productService->delete($product);

        return $this->serializer->createEmptyJsonResponse();
    }

    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function create(
        Request $request,
        array $createDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse
    {
        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            $createDeserializationGroups
        );
        $this->validator->validate($product, $createDeserializationGroups);
        $this->productService->create($product, $this->privateAuthenticationManager->getCurrentUserId());

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }


    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        array $updateDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse
    {
        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            $updateDeserializationGroups
        );
        $this->validator->validate($product, $updateDeserializationGroups);
        $product = $this->productService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($product->getUserId());
        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            $updateDeserializationGroups,
            $product
        );

        $this->productService->update($product);

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }

}

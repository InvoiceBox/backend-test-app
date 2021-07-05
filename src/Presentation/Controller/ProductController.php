<?php


namespace BackendTestApp\Presentation\Controller;


use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Application\ProductService;
use BackendTestApp\Domain\Entity\Product;
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
    ) {
    }

    #[Route('/product', name: 'find_product', methods: ['GET'])]
    public function find(
        Request $request,
        array $readSerializationGroups
    ): JsonResponse {

        $filter = new ExampleFilter($request->query->all());

        $product = $this->productService->findByFilter($filter, $this->privateAuthenticationManager->getCurrentUserId());
        return $this->serializer->createJsonResponse($product, $readSerializationGroups);


    }

    #[Route('/product/{id}', name: 'get_product', methods: ['GET'])]
    public function get(
        int $id,
        array $readSerializationGroups
    ): JsonResponse {
        $this->privateAuthenticationManager->getCurrentUserId();

        $product = $this->productService->getById($id);

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }

    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $this->privateAuthenticationManager->getCurrentUserId();

        $product = $this->productService->getById($id);

        $this->productService->delete($product);

        return $this->serializer->createEmptyJsonResponse();
    }

    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function create(
        Request $request,
        array $createDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $this->privateAuthenticationManager->getCurrentUserId();

        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            $createDeserializationGroups
        );

        $this->validator->validate($product, $createDeserializationGroups);
        $this->productService->create($product);

        return $this->serializer->createJsonResponse($product, $readSerializationGroups);
    }


    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        array $updateDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $this->privateAuthenticationManager->getCurrentUserId();

        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            $updateDeserializationGroups
        );
        $this->validator->validate($product, $updateDeserializationGroups);

        $product = $this->productService->getById($id);
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
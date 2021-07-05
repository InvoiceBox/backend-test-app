<?php


namespace BackendTestApp\Presentation\Controller;


use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Application\OrderService;
use BackendTestApp\Application\ProductService;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Entity\Orders;
use BackendTestApp\Presentation\AuthenticationManager;
use BackendTestApp\Presentation\Serializer\JsonResponse;
use BackendTestApp\Presentation\Serializer\JsonSerializer;
use BackendTestApp\Presentation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController
{
    public function __construct(
        private OrderService $orderService,
        private JsonSerializer $serializer,
        private Validator $validator,
        private AuthenticationManager $privateAuthenticationManager,
        private ProductService $productService,
    ) {
    }

    //все заказы, в которые добавлен продукт
    #[Route('/order', name: 'find_order', methods: ['GET'])]
    public function find(
        Request $request,
        array $readSerializationGroups
    ): JsonResponse {

        $filter = new ExampleFilter($request->query->all());

        $order = $this->orderService->findByFilter($filter, $this->privateAuthenticationManager->getCurrentUserId());
        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }


    #[Route('/order/{id}', name: 'get_order', methods: ['GET'])]
    public function get(
        int $id,
        array $readSerializationGroups
    ): JsonResponse {
        $order = $this->orderService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/order/{id}', name: 'delete_order', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $order = $this->orderService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        $this->orderService->delete($order);

        return $this->serializer->createEmptyJsonResponse();
    }

    #[Route('/order', name: 'create_order', methods: ['POST'])]
    public function create(
        Request $request,
        array $createDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Orders::class,
            $createDeserializationGroups
        );

        $this->validator->validate($order, $createDeserializationGroups);
        $this->orderService->create($order, $this->privateAuthenticationManager->getCurrentUserId());

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }


    #[Route('/order/{id}', name: 'update_order', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        array $updateDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Orders::class,
            $updateDeserializationGroups
        );
        $this->validator->validate($order, $updateDeserializationGroups);
        $order = $this->orderService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        $order = $this->serializer->deserialize(
            $request->getContent(),
            Orders::class,
            $updateDeserializationGroups,
            $order
        );

        $this->orderService->update($order);

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/order/{id}/addProduct_{idProduct}', name: 'insert_product_to_order', methods: ['PUT'])]
    public function insert(
        int $id,
        int $idProduct,
        array $readSerializationGroups,
    ): JsonResponse {
        $product = $this->productService->getById($idProduct);
        $order = $this->orderService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        $this->orderService->insertProduct($order, $product);

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/order/{id}/removeProduct_{idProduct}', name: 'remove_product_from_order', methods: ['PUT'])]
    public function remove(
        int $id,
        int $idProduct,
        array $readSerializationGroups,
    ): JsonResponse {
        $product = $this->productService->getById($idProduct);
        $order = $this->orderService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        $this->orderService->removeProduct($order, $product);

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }
}
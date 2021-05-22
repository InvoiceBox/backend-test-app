<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Controller;

use BackendTestApp\Application\DTO\AddToOrderRequest;
use BackendTestApp\Application\OrderService;
use BackendTestApp\Application\ProductService;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Application\DTO\OrderFilter;
use BackendTestApp\Domain\Entity\OrderItem;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Infrastructure\Repository\OrderItemRepository;
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
        private ProductService $productService,
        private JsonSerializer $serializer,
        private Validator $validator,
        private AuthenticationManager $privateAuthenticationManager
    )
    {
    }

    #[Route('/order', name: 'find_order', methods: ['GET'])]
    public function find(
        Request $request,
        array $readSerializationGroups
    ): JsonResponse
    {

        $filter = new OrderFilter($request->query->all());

        $order = $this->orderService->findByFilter($filter, $this->privateAuthenticationManager->getCurrentUserId());
        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/order/{id}', name: 'get_order', methods: ['GET'])]
    public function get(
        int $id,
        array $readSerializationGroups
    ): JsonResponse
    {
        $order = $this->orderService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/order/{id}', name: 'delete_order', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse
    {
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
    ): JsonResponse
    {
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Order::class,
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
    ): JsonResponse
    {
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Order::class,
            $updateDeserializationGroups
        );
        $this->validator->validate($order, $updateDeserializationGroups);

        $order = $this->orderService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Order::class,
            $updateDeserializationGroups,
            $order
        );

        $this->orderService->update($order);

        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }


    #[Route('/addToOrder', name: 'add_to_order', methods: ['POST'])]
    public function addToOrder(Request $request, array $readSerializationGroups): JsonResponse
    {
        $requestDetail = $this->serializer->deserialize($request->getContent(), AddToOrderRequest::class, $readSerializationGroups);
        $order = $this->orderService->getById($requestDetail->order_id);
        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());
        $product = $this->productService->getById($requestDetail->product_id);
        $item = new OrderItem();
        $item->setProduct($product);
        $order->addItem($item);
        $this->orderService->update($order);
        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }

    #[Route('/removeItemOrder', name: 'remove_itemOrder', methods: ['DELETE'])]
    public function removeItemOrder(Request $request, array $readSerializationGroups, OrderItemRepository $orderItemRepository): JsonResponse
    {
        $requestDetail = $this->serializer->deserialize($request->getContent(), AddToOrderRequest::class, $readSerializationGroups);
        $order = $this->orderService->getById($requestDetail->order_id);
        $this->privateAuthenticationManager->checkCurrentUserId($order->getUserId());
        $item = $order->getItemByProductId($requestDetail->product_id);
        $order->deleteItem($item);
        $this->orderService->update($order);
        return $this->serializer->createJsonResponse($order, $readSerializationGroups);
    }


}

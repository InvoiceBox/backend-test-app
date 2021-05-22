<?php

declare(strict_types=1);

namespace BackendTestApp\Tests\Controller;

use BackendTestApp\Infrastructure\Repository\OrderRepository;
use BackendTestApp\Infrastructure\Repository\ProductRepository;
use BackendTestApp\Tests\TestCase;

class OrderControllerTest extends TestCase
{
    protected OrderRepository $orderRepository;
    protected ProductRepository $productRepository;
    protected int $currentUserId;

    /**
     * @test
     */
    public function get()
    {
        $orderId = $this->create();
        $response = $this->doGetRequest('/api/order/' . $orderId);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($orderId, $response['data']['id']);
    }

    /**
     * @test
     */
    public function findWithAuth(): void
    {
        $this->currentUserId = mt_rand(100, 999);
        $orderId = $this->create();

        $response = $this->doGetRequest('/api/order');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($orderId, $response['data'][0]['id']);
    }

    /**
     * Create empty order
     * @test
     */
    public function create(): int
    {

        $response = $this->doPostRequest(
            '/api/order',
            []
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

        $order = $this->orderRepository->getById($response['data']['id']);
        $this->assertEquals($this->currentUserId, $order->getUserId());
        return $response['data']['id'];
    }


    /**
     * Create product
     * @test
     */
    public function createProduct(string $title = 'Title'): int
    {

        $response = $this->doPostRequest(
            '/api/product',
            [
                'title' => $title,
                'sku' => md5(uniqid((string)rand(), true)),
                'price' => mt_rand(10, 1000),
                'qty' => mt_rand(1, 100)
            ]
        );
        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

        $product = $this->productRepository->getById($response['data']['id']);
        $this->assertEquals($product->getSumQty(), $product->getPrice() * $product->getQty());
        $this->assertEquals($this->currentUserId, $product->getUserId());

        return $response['data']['id'];
    }
    /**
     * @test
     */
    public function update()
    {
        $orderId = $this->create();

        $response = $this->doPutRequest(
            '/api/order/' . $orderId,
            ['total_price' => $total_price = '0']
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($total_price, $response['data']['total_price']);
    }

    /**
     * @test
     */
    public function delete()
    {
        $orderId = $this->create();

        $response = $this->doDeleteRequest('/api/order/' . $orderId);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(null, $response['data']);
    }

    /**
     * @test
     */
    public function addProductToOrder()
    {
        $orderId = $this->create();
        $productId = $this->createProduct();
        $product = $this->productRepository->getById($productId);

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productId,
        ]);


        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);
        $this->assertTrue(count($response['data']['items'][0]['product']) > 0);

        $this->assertEquals($response['data']['items'][0]['product']['id'], $productId);
        $this->assertEquals($response['data']['items'][0]['product']['price'], $product->getPrice());

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productId,
        ]);
        $this->assertEquals($response['data']['total_price'],
            $response['data']['items'][0]['qty'] * $response['data']['items'][0]['product']['price']);

    }

    /**
     * @test
     */
    public function addMultipleProductToOrder()
    {
        $orderId = $this->create();
        $productOne = $this->productRepository->getById($this->createProduct());
        $productTwo = $this->productRepository->getById($this->createProduct());

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productOne->getId(),
        ]);

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productTwo->getId(),
        ]);

        $sumItem = array_sum(array_map(function ($item) {
            return $item['product']['price'] * $item['qty'];
        }, $response['data']['items']));

        $this->assertEquals($response['data']['total_price'], $sumItem);
    }

    /**
     * @test
     */
    public function removeProductToOrder()
    {
        $orderId = $this->create();
        $productOne = $this->productRepository->getById($this->createProduct());
        $productTwo = $this->productRepository->getById($this->createProduct());

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productOne->getId(),
        ]);

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productOne->getId(),
        ]);

        $response = $this->doPostRequest('/api/addToOrder', [
            "order_id" => $orderId,
            "product_id" => $productTwo->getId(),
        ]);

        $sumItem = array_sum(array_map(function ($item) {
            return $item['product']['price'] * $item['qty'];
        }, $response['data']['items']));

        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);
        $this->assertEquals($response['data']['items'][0]['product']['id'], $productOne->getId());
        $this->assertEquals($response['data']['total_price'], $sumItem);

        $response = $this->doDeleteRequest('/api/removeItemOrder', [
            "order_id" => $orderId,
            "product_id" => $productOne->getId(),
        ]);
        $this->assertNotEquals($response['data']['items'][0]['product']['id'], $productOne->getId());
        $this->assertNotEquals($response['data']['total_price'], $sumItem);

    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUserId = 100;
        $this->orderRepository = $this->client->getContainer()->get(OrderRepository::class);
        $this->productRepository = $this->client->getContainer()->get(ProductRepository::class);
    }

    protected function doRequest(string $method, string $url, array $params = [], array $headers = []): array
    {
        $headers['X-USER-ID'] = $this->currentUserId;

        return parent::doRequest($method, $url, $params, $headers);
    }
}

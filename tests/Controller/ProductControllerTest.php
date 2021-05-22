<?php

declare(strict_types=1);

namespace BackendTestApp\Tests\Controller;

use BackendTestApp\Infrastructure\Repository\ProductRepository;
use BackendTestApp\Tests\TestCase;

class ProductControllerTest extends TestCase
{
    protected ProductRepository $productRepository;
    protected int $currentUserId;

    /**
     * @test
     */
    public function get()
    {
        $productId = $this->create();
        $response = $this->doGetRequest('/api/product/' . $productId);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($productId, $response['data']['id']);
    }

    /**
     * @test
     */
    public function findWithAuth(): void
    {
        $this->currentUserId = mt_rand(100, 999);
        $productId = $this->create();

        $response = $this->doGetRequest('/api/product');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($productId, $response['data'][0]['id']);
    }

    /**
     * @test
     */
    public function create(string $title = 'Title'): int
    {
        $price = 120; $qty = 3;
        $response = $this->doPostRequest(
            '/api/product',
            [
                'title' => $title,
                'sku' => md5(uniqid((string)rand(), true)),
                'price' => $price,
                'qty' => $qty
            ]
        );
        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

        $product = $this->productRepository->getById($response['data']['id']);
        $this->assertEquals($product->getSumQty(), $price* $qty);
        $this->assertEquals($this->currentUserId, $product->getUserId());

        return $response['data']['id'];
    }

    /**
     * @test
     */
    public function createWrongData(): void
    {
        $response = $this->doPostRequest(
            '/api/product',
            [
                'title' => str_repeat('T', 1000),
                'sku' => md5(uniqid((string)rand(), true)),
                'price' => 200,
                'qty' => 1
            ]
        );

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('title', $response['error']['fields'][0]['name']);

        $response = $this->doPostRequest('/api/product', ['title' => 'Other title']);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('sku', $response['error']['fields'][0]['name']);
    }

    /**
     * @test
     */
    public function update()
    {
        $productId = $this->create();

        $response = $this->doPutRequest(
            '/api/product/' . $productId,
            ['title' => $newTitle = 'Updated title']
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($newTitle, $response['data']['title']);
    }

    /**
     * @test
     */
    public function delete()
    {
        $productId = $this->create();

        $response = $this->doDeleteRequest('/api/product/' . $productId);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(null, $response['data']);
    }

    /**
     * @test
     */
    public function find()
    {
        $productId = $this->create($title = 'New long title');

        $response = $this->doGetRequest("/api/product?query={$title}&_order[id]=desc");

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($productId, $response['data'][0]['id']);

        $response = $this->doGetRequest("/api/product?query=WrongTitle123&_order[id]=asc");

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(0, $response['data']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUserId = 100;
        $this->productRepository = $this->client->getContainer()->get(ProductRepository::class);
    }

    protected function doRequest(string $method, string $url, array $params = [], array $headers = []): array
    {
        $headers['X-USER-ID'] = $this->currentUserId;

        return parent::doRequest($method, $url, $params, $headers);
    }
}

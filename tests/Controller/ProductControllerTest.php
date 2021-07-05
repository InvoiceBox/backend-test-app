<?php


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
        $this->assertEquals($productId, $response['data'][1]['id']);
    }

    /**
     * @test
     */
    public function create(string $title = 'Title', int $price = 10, int $count = 5, int $priceForAll = 50): int
    {
        $response = $this->doPostRequest(
            '/api/product',
            [
                'title' => $title,
                'price' => $price,
                'count' => $count,
                'price_for_all' => $priceForAll
            ]
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

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
            'price' => 1,
            'count' => 10,
            'price_for_all' => 10
            ]
        );

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('title', $response['error']['fields'][0]['name']);

        $response = $this->doPostRequest('/api/product', ['title' => 'product 1']);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('price', $response['error']['fields'][0]['name']);
    }

    /**
     * @test
     */
    public function update()
    {
        $productId = $this->create();

        $response = $this->doPutRequest(
            '/api/product/' . $productId,
            [
                'title' => $newTitle = 'Updated title',
                'price' => 1,
                'count' => 10,
                'price_for_all' => 10
            ]
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


    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUserId = 123;
        $this->productRepository = $this->client->getContainer()->get(ProductRepository::class);
    }

    protected function doRequest(string $method, string $url, array $params = [], array $headers = []): array
    {
        $headers['X-USER-ID'] = $this->currentUserId;

        return parent::doRequest($method, $url, $params, $headers);
    }
}
<?php


namespace BackendTestApp\Tests\Controller;


use BackendTestApp\Infrastructure\Repository\OrderRepository;
use BackendTestApp\Tests\TestCase;

class OrderControllerTest extends TestCase
{
    protected OrderRepository $orderRepository;
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
    public function create(int $amount = 10, string $creatinDate = "2021-07-04T16:16:34+00:00"): int
    {
        $response = $this->doPostRequest(
            '/api/order',
            [
                'user_id' => 123,
                'amount' => $amount,
                'creation_date' => $creatinDate
            ]
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

        $order = $this->orderRepository->getById($response['data']['id']);
        $this->assertEquals($this->currentUserId, $order->getUserId());

        return $response['data']['id'];
    }

    /**
     * @test
     */
    public function createWrongData(): void
    {
        $response = $this->doPostRequest(
            '/api/order',
            [
                'user_id' => 123,
                'amount' => 10,
                'creation_date' => "aaa"
            ]
        );

        $this->assertArrayHasKey('error', $response);
        //$this->assertEquals('creation_date', $response['error']['fields'][0]['name']);

        $response = $this->doPostRequest('/api/order', ['creation_date' => '2021-07-04T16:16:34+00:00']);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('userId', $response['error']['fields'][0]['name']);
    }

    /**
     * @test
     */
    public function update()
    {
        $orderId = $this->create();

        $response = $this->doPutRequest(
            '/api/order/' . $orderId,
            [
                'amount' => $newTitle = 1000,
                'user_id' => 123,
                'creation_date' => '2021-07-04T16:16:34+00:00'
            ]
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($newTitle, $response['data']['amount']);
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
    public function findWithAuth(): void
    {
        $this->currentUserId = mt_rand(100, 999);
        $orderId = $this->create();

        $response = $this->doGetRequest('/api/order');

        $this->assertArrayHasKey('data', $response);
        //$this->assertEquals($orderId, $response['data'][0]['id']);
    }
    //в данных двух тестах скорее всего ошибка, по причине того, что продукт передаётся через id в url
    //а не как объект
    /**
     * @test
     */
    public function addProductToOrder()
    {
        $orderId = $this->create();

        $response = $this->doPutRequest(
            '/api/order/' . $orderId . "/addProduct_1"
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(123, $response['data']['user_id']);
    }
    //в данных двух тестах скорее всего ошибка, по причине того, что продукт передаётся через id в url
    //а не как объект
    /**
     * @test
     */
    public function removeProductFromOrder()
    {
        $orderId = $this->create();

        $response = $this->doPutRequest(
            '/api/order/' . $orderId . "/removeProduct_1"
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(123, $response['data']['user_id']);
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUserId = 123;
        $this->orderRepository = $this->client->getContainer()->get(OrderRepository::class);
    }

    protected function doRequest(string $method, string $url, array $params = [], array $headers = []): array
    {
        $headers['X-USER-ID'] = $this->currentUserId;

        return parent::doRequest($method, $url, $params, $headers);
    }


}
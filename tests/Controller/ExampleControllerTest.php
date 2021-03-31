<?php

declare(strict_types=1);

namespace BackendTestApp\Tests\Controller;

use BackendTestApp\Infrastructure\Repository\ExampleRepository;
use BackendTestApp\Tests\TestCase;

class ExampleControllerTest extends TestCase
{
    protected ExampleRepository $exampleRepository;
    protected int $currentUserId;

    /**
     * @test
     */
    public function get()
    {
        $exampleId = $this->create();

        $response = $this->doGetRequest('/api/example/' . $exampleId);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($exampleId, $response['data']['id']);
    }

    /**
     * @test
     */
    public function findWithAuth(): void
    {
        $this->currentUserId = mt_rand(100, 999);
        $exampleId = $this->create();

        $response = $this->doGetRequest('/api/example');

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($exampleId, $response['data'][0]['id']);
    }

    /**
     * @test
     */
    public function create(string $title = 'Title'): int
    {
        $response = $this->doPostRequest(
            '/api/example',
            [
                'title' => $title,
                'description' => 'description'
            ]
        );

        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['id'] > 0);

        $example = $this->exampleRepository->getById($response['data']['id']);
        $this->assertEquals($this->currentUserId, $example->getUserId());

        return $response['data']['id'];
    }

    /**
     * @test
     */
    public function createWrongData(): void
    {
        $response = $this->doPostRequest(
            '/api/example',
            [
                'title' => str_repeat('T', 1000),
                'description' => 'description'
            ]
        );

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('title', $response['error']['fields'][0]['name']);

        $response = $this->doPostRequest('/api/example', ['title' => 'Other title']);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('description', $response['error']['fields'][0]['name']);
    }

    /**
     * @test
     */
    public function update()
    {
        $exampleId = $this->create();

        $response = $this->doPutRequest(
            '/api/example/' . $exampleId,
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
        $exampleId = $this->create();

        $response = $this->doDeleteRequest('/api/example/' . $exampleId);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(null, $response['data']);
    }

    /**
     * @test
     */
    public function find()
    {
        $exampleId = $this->create($title = 'New long title');

        $response = $this->doGetRequest("/api/example?query={$title}&_order[id]=desc");

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($exampleId, $response['data'][0]['id']);

        $response = $this->doGetRequest("/api/example?query=WrongTitle123&_order[id]=asc");

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(0, $response['data']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentUserId = 100;
        $this->exampleRepository = $this->client->getContainer()->get(ExampleRepository::class);
    }

    protected function doRequest(string $method, string $url, array $params = [], array $headers = []): array
    {
        $headers['X-USER-ID'] = $this->currentUserId;

        return parent::doRequest($method, $url, $params, $headers);
    }
}

<?php

declare(strict_types=1);

namespace BackendTestApp\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class TestCase extends WebTestCase
{
    protected static bool $isInit = false;
    protected KernelBrowser $client;
    protected ?Application $application = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        if (!self::$isInit) {
            $this->runCommand('doctrine:database:create');
            $this->runCommand('doctrine:schema:drop --force');
            $this->runCommand('doctrine:schema:update --force');
            $this->runCommand('doctrine:fixtures:load -n');

            self::$isInit = true;
        }
    }


    protected function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);
        return $this->getApplication()->run(new StringInput($command));
    }

    protected function getApplication()
    {
        if (null === $this->application) {
            if (!$this->client) {
                $this->client = static::createClient();
            }
            $this->application = new Application($this->client->getKernel());
            $this->application->setAutoExit(false);
        }
        return $this->application;
    }


    protected function doGetRequest(string $url, array $headers = []): array
    {
        return $this->doRequest('GET', $url, [], $headers);
    }

    protected function doRequest(
        string $method,
        string $url,
        array $params = [],
        array $headers = []
    ): array {
        $this->client->request(
            $method,
            $url,
            [],
            [],
            $this->prepareHeaders($headers),
            json_encode($params)
        );
        $result = json_decode($this->client->getResponse()->getContent(), true);

        return $result ?: (array)$this->client->getResponse()->getContent();
    }

    protected function prepareHeaders(array $headers): array
    {
        $headers = array_combine(
            array_map(
                static fn(string $header): string => 'HTTP_' . strtoupper(
                        str_replace('-', '_', $header)
                    ),
                array_keys($headers)
            ),
            array_values($headers)
        );
        $headers['CONTENT_TYPE'] = 'application/json';
        $headers['HTTP_ACCEPT'] = 'application/json';

        return $headers;
    }

    protected function doPostRequest(
        string $url,
        array $params = [],
        array $headers = []
    ): array {
        return $this->doRequest('POST', $url, $params, $headers);
    }

    protected function doPutRequest(
        string $url,
        array $params = [],
        array $headers = []
    ): array {
        return $this->doRequest('PUT', $url, $params, $headers);
    }

    protected function doDeleteRequest(string $url, $params = [], array $headers = []): array
    {
        return $this->doRequest('DELETE', $url, $params, $headers);
    }
}

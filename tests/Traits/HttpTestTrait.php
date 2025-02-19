<?php

namespace TomWilford\SlimSqids\Tests\Traits;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

trait HttpTestTrait
{
    protected Psr17Factory $factory;
    protected App $app;

    protected function createApp(): void
    {
        $this->factory = new Psr17Factory();
        $this->app = new App($this->factory);
        $this->app->addBodyParsingMiddleware();
    }

    protected function createRequest(string $method, string $uri)
    {
        return $this->factory->createServerRequest($method, $uri);
    }

    protected function handleRequest($request)
    {
        return $this->app->handle($request);
    }

    /**
     * Assert that the response body contains a string.
     *
     * @param string $expected The expected string
     * @param ResponseInterface $response The response
     *
     * @return void
     */
    protected function assertResponseContains(string $expected, ResponseInterface $response): void
    {
        $body = (string)$response->getBody();

        $this->assertStringContainsString($expected, $body);
    }
}

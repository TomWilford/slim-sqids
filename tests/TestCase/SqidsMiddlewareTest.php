<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\TestCase;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Sqids\Sqids;
use TomWilford\SlimSqids\SqidsMiddleware;
use TomWilford\SlimSqids\Tests\Fixtures\TestAction;

class SqidsMiddlewareTest extends TestCase
{
    private App $app;
    private Psr17Factory $factory;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
        $this->app = new App($this->factory);
        $this->app->addBodyParsingMiddleware();
    }

    public function testShouldEncodeUriArguments()
    {
        $sqids = new Sqids();
        $encoded = $sqids->encode([123]);

        $request = $this->factory->createServerRequest('GET', '/test/' . $encoded);

        $sut = new SqidsMiddleware($sqids);
        $this->app->addMiddleware($sut);
        $this->app->addRoutingMiddleware();

        $this->app->get('/test/{testSqid}', TestAction::class);

        $response = $this->app->handle($request);

        $this->assertStringContainsString("123", (string)$response->getBody());
    }
}

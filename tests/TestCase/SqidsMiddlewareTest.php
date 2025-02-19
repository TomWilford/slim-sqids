<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\TestCase;

use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;
use TomWilford\SlimSqids\SqidsMiddleware;
use TomWilford\SlimSqids\Tests\Fixtures\TestAction;
use TomWilford\SlimSqids\Tests\Traits\HttpTestTrait;

#[UsesClass(SqidsMiddleware::class)]
class SqidsMiddlewareTest extends TestCase
{
    use HttpTestTrait;

    protected function setUp(): void
    {
        $this->createApp();
    }

    public function testMiddlewareDecodesSqidFromUrl(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}', TestAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("123", $response);
    }

    public function testMiddlewareIgnoresOtherArgumentsInUrl(): void
    {
        $sqids   = new Sqids();
        $request = $this->createRequest('GET', '/test/abc');

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{id}', TestAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("abc", $response);
    }

    public function testMiddlewareDecodesMultipleSqidsFromUrl(): void
    {
        $sqids   = new Sqids();
        $encodedA = $sqids->encode([123]);
        $encodedB = $sqids->encode([456]);
        $request = $this->createRequest('GET', '/test/' . $encodedA . '/thing/' . $encodedB);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}/thing/{thingSqid}', TestAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("123", $response);
        $this->assertResponseContains("456", $response);
    }

    public function testMiddlewareIgnoresUrlWithoutArguments()
    {
        $request = $this->createRequest('GET', '/test/without/args');

        $sqids   = new Sqids();
        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/without/args', TestAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('{"Arguments":[]}', $response);
    }
}

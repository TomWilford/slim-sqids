<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;
use TomWilford\SlimSqids\SqidsMiddleware;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsCamelCaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsKebabCaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsLowercaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsPascalCaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsSnakeCaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestAttributeIsUppercaseIdAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestMultipleAttributeAction;
use TomWilford\SlimSqids\Tests\Fixtures\Action\TestSingleAttributeAction;
use TomWilford\SlimSqids\Tests\Traits\HttpTestTrait;

#[CoversClass(SqidsMiddleware::class)]
#[UsesClass(Sqids::class)]
#[UsesClass(GlobalSqidConfiguration::class)]
class SqidsMiddlewareTest extends TestCase
{
    use HttpTestTrait;

    protected function setUp(): void
    {
        $this->createApp();
        try {
            GlobalSqidConfiguration::get();
        } catch (\RuntimeException $exception) {
            GlobalSqidConfiguration::set(new Sqids());
        }
    }

    public function testMiddlewareDecodesSqidFromUrl(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}', TestSingleAttributeAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("123", $response);
    }

    public function testMiddlewareDecodesMultipleSqidsFromUrl(): void
    {
        $sqids   = new Sqids();
        $encodedA = $sqids->encode([123]);
        $encodedB = $sqids->encode([456]);
        $request = $this->createRequest('GET', '/test/' . $encodedA . '/thing/' . $encodedB);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}/thing/{thingSqid}', TestMultipleAttributeAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("123", $response);
        $this->assertResponseContains("456", $response);
    }


    public function testMiddlewareWorksWithGlobalConfig(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware());
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}', TestSingleAttributeAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains("123", $response);
    }

    public function testGetIdInCaseReturnsLowerCaseIdForLowercaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{sqid}', TestAttributeIsLowercaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }

    public function testGetIdInCaseReturnsLowerCaseIdForSnakeCaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{test_sqid}', TestAttributeIsSnakeCaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }

    public function testGetIdInCaseReturnsLowerCaseIdForKebabCaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{test-sqid}', TestAttributeIsKebabCaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }

    public function testGetIdInCaseReturnsUppercaseIdForUppercaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{SQID}', TestAttributeIsUppercaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }

    public function testGetIdInCaseReturnsPascalCaseIdForPascalCaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{TestSqid}', TestAttributeIsPascalCaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }

    public function testGetIdInCaseReturnsPascalCaseIdForCamelCaseSqid(): void
    {
        $sqids   = new Sqids();
        $encoded = $sqids->encode([123]);
        $request = $this->createRequest('GET', '/test/' . $encoded);

        $this->app->addMiddleware(new SqidsMiddleware($sqids));
        $this->app->addRoutingMiddleware();
        $this->app->get('/test/{testSqid}', TestAttributeIsCamelCaseIdAction::class);

        $response = $this->handleRequest($request);

        $this->assertResponseContains('true', $response);
    }
}

<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Sqids\Sqids;

class SqidsMiddleware implements MiddlewareInterface
{
    public function __construct(private Sqids $sqids)
    {
        //
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if ($route !== null) {
            $arguments = $route->getArguments();
            array_walk(
                $arguments,
                fn (&$value) => $value = $this->sqids->decode($value)[0]
            );
            $route->setArguments($arguments);
        }

        return $handler->handle($request);
    }
}

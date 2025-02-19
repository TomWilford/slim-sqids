<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Sqids\Sqids;

final class SqidsMiddleware implements MiddlewareInterface
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
            $sqids = $this->sqids;
            $arguments = $route->getArguments();
            if (!empty($arguments)) {
                array_walk(
                    $arguments,
                    function (&$value, $key) use ($sqids) {
                        if (str_contains(strtolower($key), 'sqid')) {
                            $value = $sqids->decode($value)[0];
                        }
                    }
                );
                $route->setArguments($arguments);
            }
        }

        return $handler->handle($request);
    }
}

<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $result = [
            'Arguments' => $arguments,
        ];
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}

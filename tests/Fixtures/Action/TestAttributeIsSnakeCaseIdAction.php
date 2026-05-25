<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestAttributeIsSnakeCaseIdAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $result = [
            'attribute' => (bool)$request->getAttribute('test_id', false),
        ];
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}

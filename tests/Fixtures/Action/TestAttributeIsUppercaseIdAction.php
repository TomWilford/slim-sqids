<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestAttributeIsUppercaseIdAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $result = [
            'attribute' => (bool)$request->getAttribute('ID', false),
        ];
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}

<?php

declare(strict_types=1);

namespace TomWilford\SlimSqids\Tests\Fixtures\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestMultipleAttributeAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $result = [
            'attributes' => [
                $request->getAttribute('testId'),
                $request->getAttribute('thingId'),
            ],
        ];
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}

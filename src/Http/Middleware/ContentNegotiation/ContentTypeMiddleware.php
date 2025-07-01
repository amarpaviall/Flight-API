<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ContentTypeMiddleware implements MiddlewareInterface{

    public function __construct(
        private ContentTypeNegotiator $contentNegotiator
     )
    { }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{

        $request = $this->contentNegotiator->negotiate($request);

        // Handle the request..returns a Response
        $response = $handler->handle($request);

        // Return the response
        return $response->withHeader('Content-Type', $request->getAttribute('content-type')->value);

    }

}
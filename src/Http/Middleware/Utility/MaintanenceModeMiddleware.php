<?php


declare(strict_types=1);

namespace App\Http\Middleware\Utility;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class MaintanenceModeMiddleware implements MiddlewareInterface {

    public function __construct(private bool $isMaintenanceMode = false)
    {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        if (!$this->isMaintenanceMode) {
            return $handler->handle($request);
        }

       // dd($this->isMaintenanceMode);

       $response = new Response(StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE);
       $response->getBody()->write("The API is currently down for maintenance");
       $response = $response->withHeader("Retry-After", 3600);

       return $response;
    }
}
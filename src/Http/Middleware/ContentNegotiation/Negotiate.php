<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

use Psr\Http\Message\ServerRequestInterface;

interface Negotiate {

    public function negotiate(ServerRequestInterface $request) : ServerRequestInterface;
}
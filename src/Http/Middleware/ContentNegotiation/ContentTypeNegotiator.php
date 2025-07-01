<?php

declare(strict_types=1);

namespace App\Http\Middleware\ContentNegotiation;

use App\Serializer\Serializer;
use Psr\Http\Message\ServerRequestInterface;

class ContentTypeNegotiator implements Negotiate {

    public function __construct(private Serializer $serializer)
    {
    }
    
    public function negotiate(ServerRequestInterface $request) : ServerRequestInterface
     { 

        $accept = $request->getHeaderLine('Accept');

        $formats = explode(',', $accept);

        foreach($formats as $format){
           if($getFormat = ContentType::tryFrom($format))
           {
             break;
           }
        }

        $contentType = ($getFormat ?? ContentType::JSON);

        // set format on serializer

       $this->serializer->setFormat($contentType->format());

        return $request->withAttribute('content-type', $contentType );

    }
}
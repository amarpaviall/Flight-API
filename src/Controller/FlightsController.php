<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Flight;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class FlightsController extends ApiController {

   public function index(Request $request, Response $response): Response {
    
     // retrive flights
     $flights = $this->entityManager->getRepository(Flight::class)->findAll();
     
     // Handle not found resource
    if(!$flights){
     return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

     // serialize
     $jsonFlights = $this->serializer->serialize(
                     ['flights'=> $flights]
                    );

     //return the response containing the flights
     $response->getBody()->write($jsonFlights);

     //return $response->withHeader('Content-type', 'application/json');
     return $response->withHeader('Cache-Control', 'public, max-age=600');
   }


   public function show(Request $request, Response $response, string $number): Response{

    $flight = $this->entityManager->getRepository(Flight::class)
              ->findOneBy(['number' => $number]);

    // Handle not found resource
    if(!$flight){
      return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    //dd($flight);
     // serialize
     $jsonFlights = $this->serializer->serialize(['flight'=> $flight]);

     //return the response containing the flights
     $response->getBody()->write($jsonFlights);
     
     //return $response->withHeader('Content-type', 'application/json');
      return $response->withHeader('Cache-Control', 'public, max-age=600');
   }

   public function store(Request $request, Response $response) : Response
   {
      // Grab the post data
      $flightJson = $request->getBody()->getContents();

      // Deserialize into a flight
      $flight = $this->serializer->deserialize(
        $flightJson,
        Flight::class
      );

      // validate the post data before store into db
      $this->validator->validate($flight, $request, [Flight::CREATE_GROUP]);
       
      //save flight data into DB

      $this->entityManager->persist($flight); // all data hold by entity manager
      $this->entityManager->flush(); // one by one, most optimize way to insert into db

      // serialize the new flight

      $jsonFlight = $this->serializer->serialize(
        ['flight'=> $flight], 
        $request->getAttribute('content-type')->format()
      );

      $response->getBody()->write($jsonFlight);

      return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
   }

   public function destroy(Request $request, Response $response, string $number) : Response {

    $flight = $this->entityManager->getRepository(Flight::class)
              ->findOneBy(['number' => $number]);

    // Handle not found resource
    if(!$flight){
      return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    //remove from db
    $this->entityManager->remove($flight);
    $this->entityManager->flush();

    return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

   }


   public function update(Request $request, Response $response, string $number) : Response {

    // Grab the post data
      $getflight = $this->entityManager->getRepository(Flight::class)
              ->findOneBy(['number' => $number]);

      // Handle not found resource
      if(!$getflight){
        return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
      }

      // Grab the post data
      $flightJson = $request->getBody()->getContents();

      // Deserialize into a flight and map the flight
      $flight = $this->serializer->deserialize(
        data: $flightJson,
        type: Flight::class,
        context: [
          AbstractNormalizer::OBJECT_TO_POPULATE => $getflight,
          AbstractNormalizer::IGNORED_ATTRIBUTES => ['number']
        ]
      );

     // dd($flight);

     // validate the post data

     $this->validator->validate($flight, $request, [Flight::UPDATE_GROUP]);
     
     //save flight data into DB

      $this->entityManager->persist($flight); // all data hold by entity manager
      $this->entityManager->flush(); // one by one, most optimize way to insert into db

      // serialize the new flight

      $jsonFlight = $this->serializer->serialize(
        ['flight'=> $flight], 
        $request->getAttribute('content-type')->format()
      );

      $response->getBody()->write($jsonFlight);

      return $response;

   }
}
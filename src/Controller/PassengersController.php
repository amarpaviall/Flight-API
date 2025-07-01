<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Passenger;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

readonly class PassengersController extends ApiController {
   
    public function index(Request $request, Response $response): Response {
    
     // retrive passengers
     $passengers = $this->entityManager->getRepository(Passenger::class)->findAll();
     //dd($passengers);
     // Handle not found resource
    if(!$passengers){
     return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

     // serialize
     $jsonPassengers = $this->serializer->serialize(
                     ['passengers'=> $passengers]
                    );

     //return the response containing the flights
     $response->getBody()->write($jsonPassengers);

     //return $response->withHeader('Content-type', 'application/json');
     return $response->withHeader('Cache-Control', 'public, max-age=600');
   }


   public function show(Request $request, Response $response, string $reference): Response{

    $passenger = $this->entityManager->getRepository(Passenger::class)
              ->findOneBy(['reference' => $reference]);

    // Handle not found resource
    if(!$passenger){
      return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    //dd($passenger);
     // serialize
     $jsonPassenger = $this->serializer->serialize(['passenger'=> $passenger]);

     //return the response containing the flights
     $response->getBody()->write($jsonPassenger);
     
     //return $response->withHeader('Content-type', 'application/json');
      return $response->withHeader('Cache-Control', 'public, max-age=600');
   }

   public function store(Request $request, Response $response) : Response
   {
      // Grab the post data
      $passengerJson = $request->getBody()->getContents();
      
      
      // Deserialize into a passenger
      $passenger = $this->serializer->deserialize(
        $passengerJson,
        Passenger::class
      );
    
      assert($passenger instanceof Passenger);
   
      $passenger->setReference(time(). strtoupper(substr($passenger->getLastName(), 0,3 )));
      
      // validate the post data before store into db
      $this->validator->validate($passenger, $request);
       
      //save passenger data into DB

      $this->entityManager->persist($passenger); // all data hold by entity manager
      $this->entityManager->flush(); // one by one, most optimize way to insert into db

      // serialize the new passenger

      $jsonPassenger = $this->serializer->serialize(
        ['passenger'=> $passenger], 
        $request->getAttribute('content-type')->format()
      );

      $response->getBody()->write($jsonPassenger);

      return $response->withStatus(StatusCodeInterface::STATUS_CREATED);
   }

   public function destroy(Request $request, Response $response, string $reference) : Response {

    $passenger = $this->entityManager->getRepository(Passenger::class)
              ->findOneBy(['reference' => $reference]);

    // Handle not found resource
    if(!$passenger){
      return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    //remove from db
    $this->entityManager->remove($passenger);
    $this->entityManager->flush();

    return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

   }


   public function update(Request $request, Response $response, string $reference) : Response {

    // Grab the post data
      $getpassenger = $this->entityManager->getRepository(Passenger::class)
              ->findOneBy(['reference' => $reference]);

      // Handle not found resource
      if(!$getpassenger){
        return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
      }

      // Grab the post data
      $passengerJson = $request->getBody()->getContents();

      // Deserialize into a passenger and map the passenger
      $passenger = $this->serializer->deserialize(
        data: $passengerJson,
        type: Passenger::class,
        context: [
          AbstractNormalizer::OBJECT_TO_POPULATE => $getpassenger,
          AbstractNormalizer::IGNORED_ATTRIBUTES => ['referenace']
        ]
      );

     // dd($passenger);

     // validate the post data

     $this->validator->validate($passenger, $request);
     
     //save passenger data into DB

      $this->entityManager->persist($passenger); // all data hold by entity manager
      $this->entityManager->flush(); // one by one, most optimize way to insert into db

      // serialize the new passenger

      $jsonPassenger = $this->serializer->serialize(
        ['passenger'=> $passenger], 
        $request->getAttribute('content-type')->format()
      );

      $response->getBody()->write($jsonPassenger);

      return $response->withStatus(StatusCodeInterface::STATUS_OK);

   }

}
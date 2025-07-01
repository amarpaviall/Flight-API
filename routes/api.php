<?php

declare(strict_types=1);

use App\Controller\FlightsController;
use App\Controller\PassengersController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Routing\RouteCollectorProxy;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Changing the default invocation strategy on the RouteCollector component
 * will change it for every route being defined after this change being applied
 */
$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy(new RequestResponseArgs());

// Define routes
$app->get('/healthcheck', function (Request $request, Response $response) {
    $payload = json_encode(['app' => true]);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');

    // $response->getBody()->write("Hello World!");
    // return $response->withHeader('Content-Type', 'text/html');
});

$app->group('/flights', function(RouteCollectorProxy $group){
  
    $group->get('', [FlightsController::class, 'index']);

    $group->get(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [FlightsController::class, 'show']);

    $group->post('', [FlightsController::class, 'store']);


    $group->delete(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [FlightsController::class, 'destroy']);

    $group->put(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [FlightsController::class, 'update']);

    $group->patch(
        '/{number:[A-Za-z]{2}[0-9]{1,4}-[0-9]{8}}',
        [FlightsController::class, 'update']);
});


$app->group('/passengers', function(RouteCollectorProxy $group){
   
    $group->get('', [PassengersController::class, 'index']);

    $group->get(
        '/{reference:[0-9]+[A-Z]{3}}',
        [PassengersController::class, 'show']);

    $group->post('', [PassengersController::class, 'store']);


    $group->delete(
        '/{reference:[0-9]+[A-Z]{3}}',
        [PassengersController::class, 'destroy']);

    $group->put(
        '/{reference:[0-9]+[A-Z]{3}}',
        [PassengersController::class, 'update']);

    $group->patch(
        '/{reference:[0-9]+[A-Z]{3}}',
        [PassengersController::class, 'update']);
});

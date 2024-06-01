<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once '../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/name', function ($request, $response, array $args) {
		$response->getBody()->write("Funciona!");
        return $response;
    });

$app->get('/test', function ($request, $response, array $args) {
    $params = $request->getQueryParams();
    $response->getBody()->write(json_encode($params));
return $response;
});
$app->run();
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once '../vendor/autoload.php';
//php -S localhost:100 -t app
$app = AppFactory::create();
$app->post('/ingresar', function (Request $request, Response $response, $args) {
    include "controlador/UsuarioControler.php";
    $parametros = $request->getParsedBody();
    if(isset($parametros["nombre"]) && isset($parametros["clave"])){
        if(registrarIngreso($parametros["nombre"],$parametros["clave"])){
            $response->getBody()->write("Ingreso registrado correctamente.<br>");
        }
    }else{
        $response->getBody()->write("error debes colocar el usuario y contraseña<br>");
    }
        return $response;
});

$app->post("/atender",function(Request $request, Response $response, $args){
    include "controlador/MozoControler.php";


});


$app->run();
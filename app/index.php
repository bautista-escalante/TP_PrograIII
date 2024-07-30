<?php
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Dotenv\Dotenv;

require_once '../vendor/autoload.php';
require_once "controlador/SocioController.php";
require_once "controlador/MesaController.php";
require_once "controlador/ProductoController.php";
require_once "controlador/MozoController.php";
require_once "controlador/ComandaController.php";
include_once "middleware/AuthMiddleware.php";
//php -S localhost:100 -t app
$app = AppFactory::create();
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app->group("/abmEmpleado", function (RouteCollectorProxy $grupo) {
    $grupo->post("/contratar", SocioController::class . ":contratar")->add(new AuthMiddleware("socio"));
    $grupo->delete("/despedir", SocioController::class . ":despedir")->add(new AuthMiddleware("socio"));
    $grupo->put("/suspender", SocioController::class . ":suspender")->add(new AuthMiddleware("socio"));
    $grupo->put("/rotarPersonal/{id}/{nuevoPuesto}", SocioController::class . ":rotar")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarEmpleados", SocioController::class . ":listarEmpleados");
    $grupo->post("/ingresar", SocioController::class.":ingresar");
});

$app->group("/abmMesa", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarMesa", MesaController::class . ":agregarMesa")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarMesa", MesaController::class . ":borrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarMesa", MesaController::class . ":modificarMesa")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarMesas", MesaController::class . ":listarMesas");
});

$app->group("/abmProducto", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarProducto", ProductoController::class . ":agregarProducto")->add(new AuthMiddleware("socio"));
    $grupo->post("/agregarProductos", ProductoController::class . ":agregarProductos")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarProducto", ProductoController::class . ":borrarProducto")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarProducto", ProductoController::class . ":modificarProducto")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarProductos", ProductoController::class . ":listarProductos");
});

$app->group("/laComanda", function (RouteCollectorProxy $grupo){
    $grupo->post("/atender", MozoController::class.":atender")->add(new AuthMiddleware("mozo"))->add(new \Slim\Middleware\BodyParsingMiddleware());
    $grupo->post("/servir", MozoController::class.":servir")->add(new AuthMiddleware("mozo"));
    $grupo->post("/vincularFoto", MozoController::class.":vincularFoto")->add(new AuthMiddleware("mozo"));
    $grupo->post("/cocinar", ComandaController::class.":atenderCliente")->add(new AuthMiddleware("cocinero"));
    $grupo->post("/prepararTrago", ComandaController::class.":atenderCliente")->add(new AuthMiddleware("bartender"));
    $grupo->post("/servirCerveza", ComandaController::class.":atenderCliente")->add(new AuthMiddleware("cervecero"));
    $grupo->put("/cobrarMesa", ComandaController::class.":cobrar")->add(new AuthMiddleware("mozo"));
    $grupo->put("/cerrarMesa", ComandaController::class.":cerrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->post("/verMesaMasUsada", ComandaController::class.":verMesaMasUsada")->add(new AuthMiddleware("socio"));
    $grupo->put("/puntuar", ComandaController::class.":puntuar");
    $grupo->get("/estadisticas", ComandaController::class.":verEstadisticas");
    $grupo->post("/verTiempoDemora", ComandaController::class.":verTiempoDemora");
    $grupo->get("/verMejorcomentario", SocioController::class.":verMejorcomentario")->add(new AuthMiddleware("socio"));
    $grupo->get("/verPedidos", SocioController::class.":verPedidos")->add(new AuthMiddleware("socio"));
    $grupo->get("/verPedidosFueraTiempo", SocioController::class.":verPedidosFueraTiempo")->add(new AuthMiddleware("socio"));
    $grupo->get("/VerLogo", SocioController::class.":verLogo")->add(new AuthMiddleware("socio"));
});

$app->group("/extras", function (RouteCollectorProxy $grupo){
    $grupo->put("/cancelarPedido", ComandaController::class.":cancelarPedido");
    $grupo->get("/VerCancelados", SocioController::class.":VerCancelados");
});

$app->run();
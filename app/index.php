<?php
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Dotenv\Dotenv;

require_once '../vendor/autoload.php';
require_once "controlador/SocioControler.php";
require_once "controlador/UsuarioControler.php";
require_once "controlador/MesaControler.php";
require_once "controlador/ProductoControler.php";
require_once "controlador/MozoControler.php";
require_once "controlador/ComandaControler.php";
include_once "middleware/AuthMiddleware.php";
//php -S localhost:100 -t app
$app = AppFactory::create();
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app->group("/abmEmpleado", function (RouteCollectorProxy $grupo) {
    $grupo->post("/contratar", SocioControler::class . ":contratar")->add(new AuthMiddleware("socio" ));
    $grupo->delete("/despedir", SocioControler::class . ":despedir")->add(new AuthMiddleware("socio"));
    $grupo->put("/suspender", SocioControler::class . ":suspender")->add(new AuthMiddleware("socio"));
    $grupo->put("/rotarPersonal/{id}/{nuevoPuesto}", SocioControler::class . ":rotar")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarEmpleados", SocioControler::class . ":listarEmpleados");
});

$app->group("/abmUsuario", function (RouteCollectorProxy $grupo) {
    $grupo->post("/crearCuenta", UsuarioControler::class . ":crearCuenta")->add(new AuthMiddleware("socio"));
    $grupo->delete("/eliminarUsuario", UsuarioControler::class . ":eliminarUsuario")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarUsuario", UsuarioControler::class . ":modificarUsuario")->add(new AuthMiddleware("socio"));
    $grupo->post("/ingresar", UsuarioControler::class.":ingresar");
    $grupo->get("/listarUsuarios", UsuarioControler::class . ":listarUsuarios");
});

$app->group("/abmMesa", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarMesa", MesaControler::class . ":agregarMesa")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarMesa", MesaControler::class . ":borrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarMesa", MesaControler::class . ":modificarMesa")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarMesas", MesaControler::class . ":listarMesas");
});

$app->group("/abmProducto", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarProducto", ProductoControler::class . ":agregarProducto")->add(new AuthMiddleware("socio"));
    $grupo->post("/agregarProductos", ProductoControler::class . ":agregarProductos")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarProducto", ProductoControler::class . ":borrarProducto")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarProducto", ProductoControler::class . ":modificarProducto")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarProductos", ProductoControler::class . ":listarProductos");
});

$app->group("/laComanda", function (RouteCollectorProxy $grupo){
    $grupo->post("/atender", MozoControler::class.":atender")->add(new AuthMiddleware("mozo"))->add(new \Slim\Middleware\BodyParsingMiddleware());
    $grupo->post("/servir", MozoControler::class.":servir")->add(new AuthMiddleware("mozo"));
    $grupo->post("/vincularFoto", MozoControler::class.":vincularFoto")->add(new AuthMiddleware("mozo"));
    $grupo->post("/cocinar", ComandaControler::class.":atenderCliente")->add(new AuthMiddleware("cocinero"));
    $grupo->post("/prepararTrago", ComandaControler::class.":atenderCliente")->add(new AuthMiddleware("bartender"));
    $grupo->post("/servirCerveza", ComandaControler::class.":atenderCliente")->add(new AuthMiddleware("cervecero"));
    $grupo->put("/cobrarMesa", ComandaControler::class.":cobrar")->add(new AuthMiddleware("mozo"));
    $grupo->put("/cerrarMesa", ComandaControler::class.":cerrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->post("/verMesaMasUsada", ComandaControler::class.":verMesaMasUsada")->add(new AuthMiddleware("socio"));
    $grupo->put("/puntuar", ComandaControler::class.":puntuar");
    $grupo->get("/estadisticas", ComandaControler::class.":verEstadisticas");
    $grupo->post("/verTiempoDemora", ComandaControler::class.":verTiempoDemora");
    $grupo->get("/verMejorcomentario", SocioControler::class.":verMejorcomentario")->add(new AuthMiddleware("socio"));
    $grupo->get("/verPedidos", SocioControler::class.":verPedidos")->add(new AuthMiddleware("socio"));
});

$app->group("/extras", function (RouteCollectorProxy $grupo){
    $grupo->put("/cancelarPedido", ComandaControler::class.":cancelarPedido");
    $grupo->get("/VerCancelados", SocioControler::class.":VerCancelados");
});

$app->run();
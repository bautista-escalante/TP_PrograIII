<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require_once '../vendor/autoload.php';
require_once "controlador/SocioControler.php";
require_once "controlador/UsuarioControler.php";
require_once "controlador/MesaControler.php";
require_once "controlador/ProductoControler.php";
require_once "controlador/ComandaControler.php";
include_once "middleware/AuthMiddleware.php";
//php -S localhost:100 -t app
$app = AppFactory::create();

$app->group("/abmEmpleado", function (RouteCollectorProxy $grupo) {
    $grupo->post("/contratar", SocioControler::class . ":contratar")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->delete("/despedir/{id}", SocioControler::class . ":despedir")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->put("/suspender/{id}", SocioControler::class . ":suspender")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->put("/rotarPersonal/{id}/{nuevoPuesto}", SocioControler::class . ":rotar")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->get("/listarEmpleados/{puesto}", SocioControler::class . ":listarEmpleados");
});

$app->group("/abmUsuario", function (RouteCollectorProxy $grupo) {
    $grupo->post("/crearCuenta", UsuarioControler::class . ":crearCuenta")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->delete("/eliminarUsuario/{id}", UsuarioControler::class . ":eliminarUsuario")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->put("/modificarUsuario/{id}/{nombre}/{clave}", UsuarioControler::class . ":modificarUsuario")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->get("/listarUsuarios", UsuarioControler::class . ":listarUsuarios");
    $grupo->post("/ingresar", UsuarioControler::class.":ingresar");
})->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));

$app->group("/abmMesa", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarMesa", MesaControler::class . ":agregarMesa")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->delete("/borrarMesa/{id}", MesaControler::class . ":borrarMesa")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->put("/modificarMesa/{id}/{puntos}", MesaControler::class . ":modificarMesa")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->get("/listarMesas", MesaControler::class . ":listarMesas");
});

$app->group("/abmProducto", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarProducto", ProductoControler::class . ":agregarProducto")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->delete("/borrarProducto/{id}", ProductoControler::class . ":borrarProducto")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->put("/modificarProducto/{id}/{precio}", ProductoControler::class . ":modificarProducto")->add(new AuthMiddleware("socio", obtenerUltimoPuesto()));
    $grupo->get("/listarProductos", ProductoControler::class . ":listarProductos");
});

$app->group("/laComanda",function (RouteCollectorProxy $grupo) {
    $grupo->post("atender", ComandaControler::class.":atender")->add(new AuthMiddleware("mozo",obtenerUltimoPuesto()));
    $grupo->post("/servir", MozoControler::class.":servir")->add(new AuthMiddleware("mozo",obtenerUltimoPuesto()));
    $grupo->post("/cocinar", ComandaControler::class.":Cocinar")->add(new AuthMiddleware("cocinero",obtenerUltimoPuesto()));
    $grupo->post("/prepararTrago", ComandaControler::class.":prepararTrago")->add(new AuthMiddleware("bartender",obtenerUltimoPuesto()));
    $grupo->post("/servirCerveza", ComandaControler::class.":servirCerveza")->add(new AuthMiddleware("cervecero",obtenerUltimoPuesto()));
});

$app->run();
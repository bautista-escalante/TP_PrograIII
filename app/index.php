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
$app->addBodyParsingMiddleware();

$app->group("/abmEmpleado", function (RouteCollectorProxy $grupo) {
    $grupo->post("/contratar", SocioControler::class . ":contratar")->add(new AuthMiddleware("socio" ));
    $grupo->delete("/despedir/{id}", SocioControler::class . ":despedir")->add(new AuthMiddleware("socio"));
    $grupo->put("/suspender/{id}", SocioControler::class . ":suspender")->add(new AuthMiddleware("socio"));
    $grupo->put("/rotarPersonal/{id}/{nuevoPuesto}", SocioControler::class . ":rotar")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarEmpleados/{puesto}", SocioControler::class . ":listarEmpleados");
});

$app->group("/abmUsuario", function (RouteCollectorProxy $grupo) {
    $grupo->post("/crearCuenta", UsuarioControler::class . ":crearCuenta")->add(new AuthMiddleware("socio"));
    $grupo->delete("/eliminarUsuario/{id}", UsuarioControler::class . ":eliminarUsuario")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarUsuario/{id}/{nombre}/{clave}", UsuarioControler::class . ":modificarUsuario")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarUsuarios", UsuarioControler::class . ":listarUsuarios");
    $grupo->post("/ingresar", UsuarioControler::class.":ingresar");
});

$app->group("/abmMesa", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarMesa", MesaControler::class . ":agregarMesa")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarMesa/{id}", MesaControler::class . ":borrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarMesa/{id}/{puntos}", MesaControler::class . ":modificarMesa")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarMesas", MesaControler::class . ":listarMesas");
});

$app->group("/abmProducto", function (RouteCollectorProxy $grupo) {
    $grupo->post("/agregarProducto", ProductoControler::class . ":agregarProducto")->add(new AuthMiddleware("socio"));
    $grupo->delete("/borrarProducto/{id}", ProductoControler::class . ":borrarProducto")->add(new AuthMiddleware("socio"));
    $grupo->put("/modificarProducto/{id}/{precio}", ProductoControler::class . ":modificarProducto")->add(new AuthMiddleware("socio"));
    $grupo->get("/listarProductos", ProductoControler::class . ":listarProductos");
});

$app->group("/laComanda",function (RouteCollectorProxy $grupo) {
    $grupo->post("/atender", MozoControler::class.":atender")->add(new AuthMiddleware("mozo"));
    $grupo->post("/servir", MozoControler::class.":servir")->add(new AuthMiddleware("mozo"));
    $grupo->post("/cocinar", ComandaControler::class.":Cocinar")->add(new AuthMiddleware("cocinero"));
    $grupo->post("/prepararTrago", ComandaControler::class.":prepararTrago")->add(new AuthMiddleware("bartender"));
    $grupo->post("/servirCerveza", ComandaControler::class.":servirCerveza")->add(new AuthMiddleware("cervecero"));
    $grupo->put("/cerrarMesa/{id}", ComandaControler::class.":cerrarMesa")->add(new AuthMiddleware("socio"));
    $grupo->put("/cobrarMesa/{id}", ComandaControler::class.":cobrar")->add(new AuthMiddleware("socio"));
    $grupo->put("/puntuar", ComandaControler::class.":puntuar");
});

$app->run();
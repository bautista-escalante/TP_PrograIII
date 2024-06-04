<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
/* 
put -> actualizar/modificar
post -> crear
get -> leer
delete -> borrar
*/
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
// revisar
$app->post("/atender",function(Request $request, Response $response, $args){
    include "controlador/MozoControler.php";
    include "controlador/CocineroControler.php";
    $parametros = $request->getParsedBody();
    if(isset($parametros["pedido"]) && !empty($parametros["pedido"])&&
    isset($parametros["nombre"]) && !empty($parametros["nombre"])){
        atender($parametros["pedido"],$parametros["nombre"]);
        //cocinar($parametros["pedido"]);
    }
    else{
        $response->getBody()->write("error ingresa tu pedido.<br>");
    }
    return $response;
});

$app->post("/servir",function(Request $request, Response $response, $args){
    include "controlador/MozoControler.php";
    servir();
        $response->getBody()->write("error ingresa tu pedido.<br>");
        return $response;
});

$app->post("/cocinar",function(Request $request, Response $response, $args){
    include "controlador/CocineroControler.php";
    
    $response->getBody()->write("error ingresa tu pedido.<br>");
    return $response;
});

$app->post("/contratar",function(Request $request, Response $response, $args){
    include "modelo/Socio.php";
    $parametros = $request->getParsedBody();
    if(isset($parametros["nombre"]) && !empty($parametros["nombre"]) &&isset($parametros["puesto"]) 
    & !empty($parametros["puesto"])){
        Socio::contratarEmpleado($parametros["nombre"],$parametros["puesto"]);
    }
    else{
        $response->getBody()->write("error coloca los parameros para contratar empleados.<br>");
    }
    return $response;
});

$app->delete("/despedir/{id}", function (Request $request, Response $response, $args) {
    require_once "modelo/Socio.php";
    $id = $args['id'];
    if (!empty($id)) {
        Socio::despedirEmpleado($id);
        $response->getBody()->write("Empleado despedido correctamente.<br>");
    } else {
        $response->getBody()->write("Error: coloca los parámetros para despedir al empleado.<br>");
    }
    return $response;
});
//revisar
$app->post("/suspender",function(Request $request, Response $response, $args){
    try{
        include "controlador/SocioControler.php";
        $parametros = $request->getParsedBody();
        if(isset($parametros["id"]) && !empty($parametros["id"])){
            suspender(intval($parametros["id"]));
            $response->getBody()->write("Empleado suspendido.<br>");
        }
        else{
            $response->getBody()->write("error coloca los parameros para despedir al empleado.<br>");
        }
    }catch(PDOException){
        $response->getBody()->write("error.<br>");    
    }finally{
        return $response;
    }
});

$app->get("/ver/pedidosCancelados",function(Request $request, Response $response, $args){
        include "modelo/Socio.php";
        $response->getBody()->write(Socio::VerCancelados()."<br>");
        return $response;
});

$app->get("/ver/producto/menosVendido",function(Request $request, Response $response, $args){
        include "modelo/Socio.php";
        $response->getBody()->write(Socio::VerProductoMenosVendido()."<br>");
        return $response;
});

$app->get("/ver/producto/masVendido",function(Request $request, Response $response, $args){
        include "modelo/Socio.php";
        $response->getBody()->write(Socio::VerProductoMasVendido()."<br>");
        return $response;
});

$app->get("/ver/operaciones",function(Request $request, Response $response, $args){
        include "modelo/Socio.php";
        $parametro = $request->getQueryParams();
        $response->getBody()->write(Socio::verOperaciones($parametro["puesto"])."<br>");
        return $response;
});






$app->run();
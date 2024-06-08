<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
/* 
2do Sprint ( Entrega 11 de Junio)
❖ ----Usar MiddleWare de usuarios/perfiles----
❖ ----Verificar usuarios para las tareas de abm----
❖ Manejo del estado del pedido */
require_once '../vendor/autoload.php';
require_once "controlador/UsuarioControler.php";
include_once "middleware/AuthMiddleware.php";
//php -S localhost:100 -t app
$app = AppFactory::create();

$app->post('/ingresar', function (Request $request, Response $response, $args) {
    require_once "controlador/UsuarioControler.php";
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

// alta empleado
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
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// baja empleado
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
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// modificacion empleado
$app->put("/suspender/{id}",function(Request $request, Response $response, $args){
    try{
        include "controlador/SocioControler.php";
        $parametros = $request->getParsedBody();
        $id = $args['id'];
        if(isset($id) && !empty($id)){
            Socio::suspenderEmpleado(intval($id));
            $response->getBody()->write("Empleado suspendido.<br>");
        }
        else{
            $response->getBody()->write("error coloca los parameros para suspender al empleado.<br>");
        }
    }catch(PDOException){
        $response->getBody()->write("error.<br>");    
    }finally{
        return $response;
    }
})->add(new AuthMiddleware(obtenerUltimoInicio()));
$app->get("/listarEmpleados/{cocinero}",function(Request $request, Response $response, $args){
    include "modelo/Empleado.php";
    $empleados = Empleado::obtenerEmpleadosPorPuesto($args["cocinero"]);
    foreach($empleados as $empleado){
        $response->getBody()->write("nombre: ".$empleado["nombre"].".<br>");
    }
    return $response;
});

// alta usuario
$app->post("/crearCuenta",function(Request $request, Response $response, $args){
    include "modelo\Usuario.php";
    $param = $request->getParsedBody();
    if(isset($param["nombre"],$param["puesto"],$param["clave"]) && !empty($param["nombre"])  
    && !empty($param["puesto"]) && !empty($param["clave"])){
        $nuevoUsuario = new  Usuario($param["nombre"],$param["puesto"],$param["clave"]);
        $nuevoUsuario->crearUsuario();
        $response->getBody()->write("alta de usuario exito.<br>");
    }
    else{
        $response->getBody()->write("error coloca los parameros para crear una cuenta.<br>");
    }
    return $response;
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// baja usuario
$app->delete("/eliminarUsuario/{id}", function (Request $request, Response $response, $args) {
    require_once "modelo/Usuario.php";
    $id = $args['id'];
    if (!empty($id)) {
        Usuario::borrarUsuario($id);
        $response->getBody()->write("usuario borrado correctamente.<br>");
    } else {
        $response->getBody()->write("Error: coloca los parámetros para eliminar la cuenta .<br>");
    }
    return $response;
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// modificacion usuario
$app->put("/ModificarUsuario/{id}/{nombre}/{clave}",function(Request $request, Response $response, $args){
    try{
        require_once "modelo/Usuario.php";
        if(!empty($args['id'])&&!empty($args['nombre'])&&!empty($args['clave'])){
            Usuario::modificarUsuario($args["id"],$args["clave"],$args["nombre"]);
            $response->getBody()->write("usuario modificado .<br>");
        }
        else{
            $response->getBody()->write("error coloca los parameros para modificar la cuenta.<br>");
        }
    } catch(PDOException $e) {
        $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
    }finally{
        return $response;
    }
})->add(new AuthMiddleware(obtenerUltimoInicio()));
$app->get("/listarUsuarios",function(Request $request, Response $response, $args){
    include "modelo/Usuario.php";
    $usuarios = Usuario::obtenerTodos();
    foreach($usuarios as $usuario){
        $response->getBody()->write("nombre: ".$usuario["usuario"]."<br>puesto: ".$usuario["puesto"].".<br>");
    }
    return $response;
});

// alta mesa
$app->post("/agregarMesa",function(Request $request, Response $response, $args){
    include "modelo/Mesa.php";
    $mesa = new Mesa();
    $mesa->guardar();
    $response->getBody()->write("mesa agregada correctamente.<br>");
    return $response;
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// baja mesa
$app->delete("/borrarMesa/{id}", function (Request $request, Response $response, $args) {
    require_once "modelo/Mesa.php";
    $id = $args['id'];
    if (!empty($id)) {
        Mesa::borrarMesa($id);
        $response->getBody()->write("mesa borrada correctamente.<br>");
    } else {
        $response->getBody()->write("Error: coloca los parámetros para borrar la mesa.<br>");
    }
return $response;
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// modificacion mesa
$app->put("/modificarMesa/{id}/{puntos}",function(Request $request, Response $response, $args){
    require_once "modelo/Mesa.php";
    try{
        $id = $args['id'];
        if(isset($id) && !empty($id)){
            Mesa::modificarMesa($id,$args["puntos"]);
            $response->getBody()->write("mesa modificada.<br>");
        }
        else{
            $response->getBody()->write("error coloca los parameros para modificar la mesa.<br>");
        }
    }catch(PDOException){
        $response->getBody()->write("error.<br>");    
    }finally{
        return $response;
    }
})->add(new AuthMiddleware(obtenerUltimoInicio()));
$app->get("/listarMesas",function(Request $request, Response $response, $args){
    include "modelo/Mesa.php";
    $mesas = Mesa::MostarMesas();
    foreach($mesas as $mesa){
        $response->getBody()->write("codigo: ".$mesa["codigoMesa"]."<br>estado: ".$mesa["estado"].".<br>");
    }
    return $response;
});

// alta producto
$app->post("/agregarProducto",function(Request $request, Response $response, $args){
    include "modelo/Producto.php";
    $param = $request->getParsedBody();
    if(isset($param["nombre"],$param["puesto"],$param["precio"]) && 
        !empty($param["nombre"]) && !empty($param["puesto"]) && !empty($param["precio"])){
        $producto = new Producto($param["nombre"],$param["puesto"],$param["precio"]);
        $producto->guardar();
        $response->getBody()->write("producto agregado correctamente.<br>");
        return $response;
    } else{
        $response->getBody()->write("agrega los atributos para dar el alta<br>.<br>");
    }
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// baja producto
$app->delete("/borrarProducto/{id}", function (Request $request, Response $response, $args) {
    require_once "modelo/Producto.php";
    $id = $args['id'];
    if (!empty($id)) {
        Producto::eliminarProducto($id);
        $response->getBody()->write("mesa borrada correctamente.<br>");
        } else {
            $response->getBody()->write("Error: coloca los parámetros para borrar la mesa.<br><br>");
    }
return $response;
})->add(new AuthMiddleware(obtenerUltimoInicio()));
// modificacion producto
$app->put("/modificarProducto/{id}/{precio}",function(Request $request, Response $response, $args){
    require_once "modelo/Producto.php";
    try{
        if(isset($args['id'],$args['precio']) && !empty($args['id'])){
            Producto::modificarPrecioProducto($args['id'],$args["precio"]);
            $response->getBody()->write("precio actualizado.<br>");
        }
        else{
            $response->getBody()->write("error coloca los parameros para actualizar los precios.<br>");
        }
    }catch(PDOException){
        $response->getBody()->write("error al actualizar oprecio.<br>");    
    }finally{
        return $response;
    }
})->add(new AuthMiddleware(obtenerUltimoInicio()));
$app->get("/listarProductos",function(Request $request, Response $response, $args){
    include "modelo/Producto.php";
    $productos = Producto::mostrarProductos();
    foreach($productos as $producto){
        $response->getBody()->write("nombre: ".$producto["nombre"]."<br>precio: ".$producto["precio"].".<br><br>");
    }
    return $response;
});

$app->run();
<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Usuario.php";
include_once "db/AccesoDatos.php";

class UsuarioControler {
    public function crearCuenta(Request $request, Response $response, $args) {
        $param = $request->getParsedBody();
        if (isset($param["nombre"], $param["puesto"], $param["clave"]) && 
            !empty($param["nombre"]) && !empty($param["puesto"]) && !empty($param["clave"])) {
            $nuevoUsuario = new Usuario($param["nombre"], $param["puesto"], $param["clave"]);
            $nuevoUsuario->crearUsuario();
            $response->getBody()->write("Alta de usuario exitosa.<br>");
            return $response->withStatus(201);
        } else {
            $response->getBody()->write("Error: coloca los parámetros para crear una cuenta.<br>");
            return $response->withStatus(400);
        }
    }

    public function eliminarUsuario(Request $request, Response $response, $args) {
        $id = $args['id'];
        if (!empty($id)) {
            Usuario::borrarUsuario($id);
            $response->getBody()->write("Usuario borrado correctamente.<br>");
            return $response->withStatus(200); 
        } else {
            $response->getBody()->write("Error: coloca los parámetros para eliminar la cuenta.<br>");
            return $response->withStatus(400); 
        }
    }

    public function modificarUsuario(Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $nombre = $args['nombre'];
            $clave = $args['clave'];
            if (!empty($id) && !empty($nombre) && !empty($clave)) {
                Usuario::modificarUsuario($id, $clave, $nombre);
                $response->getBody()->write("Usuario modificado correctamente.<br>");
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("Error: coloca los parámetros para modificar la cuenta.<br>");
                return $response->withStatus(400);
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }

    public function listarUsuarios(Request $request, Response $response, $args) {
        $usuarios = Usuario::obtenerTodos();
        foreach ($usuarios as $usuario) {
            $response->getBody()->write("Nombre: " . $usuario["usuario"] . "<br>Puesto: " . $usuario["puesto"] . ".<br>");
        }
        return $response->withStatus(200);
    }
    public function ingresar(Request $request, Response $response, $args){
        $parametros = $request->getParsedBody();
        if(isset($parametros["nombre"]) && isset($parametros["clave"])){
            if(registrarIngreso($parametros["nombre"],$parametros["clave"])){
                $response->getBody()->write("Ingreso registrado correctamente.<br>");
                return $response->withStatus(200);
            }
        }else{
            $response->getBody()->write("error debes colocar el usuario y contraseña<br>");
            return $response->withStatus(400);
        }
    }
}

function registrarIngreso($nombre, $clave){
    $usuario = Usuario::obtenerUsuario($nombre);
    $nuevoRegistro = array(
        "id" => $usuario["id"],
        "nombre"=>$nombre,
        "puesto"=>$usuario["puesto"],
        "fecha"=> date("y-m-d h:m:s")
    );
    $archivo = file_get_contents("modelo/Registro.json");
    $registros = json_decode($archivo,true);
    if($usuario["usuario"] == $nombre && $usuario["clave"] == $clave){
        if(!empty($registros)){
            $registros [] = $nuevoRegistro;
        }else{
            $registros = $nuevoRegistro;
        }
        return file_put_contents("modelo/Registro.json", json_encode($registros, JSON_PRETTY_PRINT));
    }else{
        echo "el usuario no existe";
        return false;
    }
}
/* function esSocio($nombre){
    $usuario = Usuario::obtenerUsuario($nombre);
    if($usuario["puesto"]=== "socio"){
        return true;
    }
    echo "no es socio";
    return false;
} */
function obtenerUltimoInicio(){
    $archivo = file_get_contents("modelo/Registro.json");
    $ingresos = json_decode($archivo, true);
    $ultimoIngreso = end($ingresos);
    return $ultimoIngreso;
}
function obtenerUltimoPuesto(){
    $ingreso = obtenerUltimoInicio();
    return $ingreso["puesto"];
}
function obtenerUltimoId(){
    $ingreso = obtenerUltimoInicio();
    return $ingreso["id"];
}
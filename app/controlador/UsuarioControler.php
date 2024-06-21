<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
include_once "modelo/Usuario.php";
include_once "db/AccesoDatos.php";
include_once "modelo/Registador.php";
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
    public function listarUsuarios(Request $request, Response $response, $args){
        $usuarios = Usuario::obtenerTodos();
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Nombre', 'puesto']);
        foreach ($usuarios as $usuario){
            fputcsv($csv, [$usuario["usuario"], $usuario["puesto"]]);
        }
        rewind($csv);
        $response->getBody()->write(stream_get_contents($csv));
        fclose($csv);
        return $response->withHeader('Content-Type', 'text/csv')
                        ->withHeader('Content-Disposition', 'attachment; filename="usuarios.csv"');
    }
    public function ingresar(Request $request, Response $response, $args){
        $parametros = $request->getParsedBody();
        if(isset($parametros["nombre"]) && isset($parametros["clave"])){
            $token = $this->registrarIngreso($parametros["nombre"],$parametros["clave"]);
            if($token != false){
                $response->getBody()->write(json_encode(["JWT"=>$token]));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }else{
            $response->getBody()->write("error debes colocar el usuario y contraseña<br>");
            return $response->withStatus(400);
        }
        return $response;
    }
    private function registrarIngreso($nombre, $clave){
        $usuario = Usuario::obtenerUsuario($nombre);
        if(empty($usuario)){
            echo "usuario inexistente o despedido<br>";
        }
        else if(!password_verify($clave, $usuario["clave"])){
            echo "clave incorreta";
            return false;
        }
        if(!empty($usuario)){
            try{
                //registar accion
                $puesto = $usuario["puesto"];
                if ($puesto != "socio"){
                    $log = new registrador();
                    $log->registarActividad("{$nombre} inicio sesion como {$puesto}");
                }
                $payload = [
                    'iat' => time(), 
                    'exp' => time() + 1800, // 30 min
                    'sector' => $puesto,
                    'idEmpleado'=> $usuario["id"]];
                return JWT::encode($payload, $_ENV["secretKey"], 'HS256');
            }catch (Exception){
                echo "error al crear el token";
            }
        }
        else{
            echo "error no tienes cuenta dentro del sistema";
        }
        return false;
    }
}
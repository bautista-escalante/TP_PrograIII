<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Socio.php";
include_once "modelo/Empleado.php";
class SocioControler {
    public function contratar(Request $request, Response $response, $args) {
        $parametros = $request->getParsedBody();
        if(isset($parametros["nombre"]) && !empty($parametros["nombre"]) && isset($parametros["puesto"]) && !empty($parametros["puesto"])){
            Socio::contratarEmpleado($parametros["nombre"], $parametros["puesto"]);
        } else {
            $response->getBody()->write("Error: coloca los parámetros para contratar empleados.<br>");
        }
        return $response;
    }
    public function despedir(Request $request, Response $response, $args) {
        $id = $args['id'];
        if (!empty($id)) {
            Socio::despedirEmpleado($id);
            $response->getBody()->write("Empleado despedido correctamente.<br>");
        } else {
            $response->getBody()->write("Error: coloca los parámetros para despedir al empleado.<br>");
        }
        return $response;
    }
    public function suspender(Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            if (isset($id) && !empty($id)) {
                Socio::suspenderEmpleado(intval($id));
                $response->getBody()->write("Empleado suspendido.<br>");
            } else {
                $response->getBody()->write("Error: coloca los parámetros para suspender al empleado.<br>");
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
        }
        return $response;
    }
    public function listarEmpleados(Request $request, Response $response, $args) {
        $puesto = $args["puesto"];
        $empleados = Empleado::obtenerEmpleadosPorPuesto($puesto);
        foreach ($empleados as $empleado) {
            $response->getBody()->write("Nombre: " . $empleado["nombre"] . ".<br>");
        }
        return $response;
    }
    public function rotar(Request $request, Response $response, $args){
        try{
            Empleado::rotarPersonal($args["id"], $args["nuevoPuesto"]);
            return $response->withStatus(200);
        } catch(Exception $e){
            return $response->withStatus(400);
        }
    }
}


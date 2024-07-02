<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Socio.php";
include_once "modelo/Empleado.php";
class SocioControler {
    public function contratar(Request $request, Response $response, $args) {
        $parametros = $request->getParsedBody();
        if(isset($parametros["nombre"],$parametros["puesto"],$parametros["clave"]) && !empty($parametros["nombre"]) && !empty($parametros["puesto"])){
            Socio::contratarEmpleado($parametros["nombre"], $parametros["puesto"]);
            $nuevoUsuario = new Usuario($parametros["nombre"], $parametros["puesto"], $parametros["clave"]);
            $mensaje = $nuevoUsuario->crearUsuario();
            $response->getBody()->write(json_encode(["ESTADO"=>$mensaje]));
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function despedir(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $id = $params['id'];
        if (!empty($id)) {
            $mensaje = Socio::despedirEmpleado($id);
            $response->getBody()->write(json_encode(["ESTADO"=>$mensaje]));
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function suspender(Request $request, Response $response, $args) {
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params['id'];
            if (isset($id) && !empty($id)) {
                Socio::suspenderEmpleado(intval($id));
                $response->getBody()->write("Empleado suspendido.<br>");
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["ERROR"=> $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function listarEmpleados(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $puesto = $params["puesto"];
        $empleados = Empleado::obtenerEmpleadosPorPuesto($puesto);
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Nombre', 'tipo', 'puntuacion']);
        foreach ($empleados as $empleado){
            fputcsv($csv, [$empleado["nombre"], $empleado["tipo"], $empleado["puntuacion"]]);
        }
        rewind($csv);
        $response->getBody()->write(stream_get_contents($csv));
        fclose($csv);
        return $response->withHeader('Content-Type', 'text/csv')
                        ->withHeader('Content-Disposition', 'attachment; filename="'.$puesto.'.csv"');
    }
    public function rotar(Request $request, Response $response, $args){
        try{
            parse_str(file_get_contents('php://input'), $params);
            if(isset($params["id"], $params["nuevoPuesto"]) && !empty($params["id"] && !empty($params["nuevoPuesto"]))){
                Empleado::rotarPersonal($params["id"], $params["nuevoPuesto"]);
                $response->getBody()->write(json_encode(["MENSAJE"=>"el personal ha rotado"]));
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
            }
        } catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>"al rotar el personal"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verPedidos(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        if(!empty($params["id"])){
            $estado = Socio::verTiempoPedido($params["id"]);
            if(!empty($estado)){
                if($estado !== null){
                    $response->getBody()->write(json_encode(["ESTADO"=>$estado]));
                }else{
                    $response->getBody()->write(json_encode(["ESTADO"=>"el empleado todabia no le asigno tiempo"]));
                }
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"id invalido"]));
            }
        }else{

            $pedidos = Socio::verPedido();
            if(!empty($pedidos)){
                foreach($pedidos as $pedido){
                    $producto = Producto::obtenerProducto(intval($pedido["idProducto"]));
                    if(!empty($producto)){

                        $response->getBody()->write(json_encode(["id"=>$pedido["id"],
                                                                "COMIDA"=>$producto["nombre"],
                                                                "PRECIO"=>$producto["precio"]]));
                    }else{
                        $response->getBody()->write(json_encode(["ERROR"=>"ese producto esta eliminado"]));
                    }
                }
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"no hay pedidos"]));

            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verMejorcomentario(Request $request, Response $response){
        try{
            $resultado = Pedido::verMejorcomentario();
            $response->getBody()->write(json_encode(["Mejores Comentarios"=>$resultado]));
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function VerCancelados(Request $request, Response $response){
        try{
            $resultados = Socio::VerCancelados();
            
            foreach($resultados as $resultado){
                $response->getBody()->write(json_encode(["PEDIDOS CANCELADOS"=>($resultado)]));
            }
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
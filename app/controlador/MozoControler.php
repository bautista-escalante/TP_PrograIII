<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "db/AccesoDatos.php";
include_once "modelo/Empleado.php";
class MozoControler{
    public function atender(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $mesa = Mesa::AsignarMesa();

        foreach($params as $param){
            if (isset($param["nombreProducto"], $param["cantidad"]) && !empty($param["cantidad"]) && !empty($param["nombreProducto"])) {
                if($mesa !== null){
                    try{
                        $cantidad = intval($param["cantidad"]);
                        for($i = 0; $i < $cantidad; $i++){
                            $alfa = Pedido::generarCodigo();
                            Empleado::atenderCliente($param["nombreProducto"], $mesa, $alfa);
                            $response->getBody()->write(json_encode(["NOMBRE"=>$param["nombreProducto"],                       
                                                                    "CODIGO ALFANUMERICO"=>$alfa,
                                                                    "MESA"=>$mesa]));
                        }
                        Mesa::ActualizarEstadoMesa($mesa, "con cliente esperando pedido");    
                    }catch(Exception $e){
                        $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
                    }
                }else{
                    $response->getBody()->write(json_encode(["ERROR"=>"no hay mesa disponible"]));
                }
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function vincularFoto(Request $request, Response $response) {
        $param = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        if (isset($files['foto'], $param['codigoAlfa']) && $files['foto']->getError() === UPLOAD_ERR_OK) {
            $nombre = $param['codigoAlfa'] . "_" . date("Y-m-d") . ".jpg";
            $ruta =  "imagenes/" . $nombre;
            $log = new registrador();
            if (move_uploaded_file($files['foto']->getStream()->getMetadata('uri'), $ruta)) {
                if(Pedido::VerificarCodigoAlfa($param['codigoAlfa'])){

                    $response->getBody()->write("Foto subida exitosamente.");
                    $log->registarActividad("el mozo vinculó el pedido con una foto");
                    return $response->withStatus(200);
                }else{
                    $response->getBody()->write("codigo invalido.");
                    return $response->withStatus(500);
                }
            } else {
                $response->getBody()->write("Error al mover el archivo subido.");
                $log->registrarError("Error al mover el archivo subido");
                return $response->withStatus(500);
            }
        } else {
            $response->getBody()->write("Datos de entrada inválidos.");
            return $response->withStatus(400);
        }
    }
    public function servir(Request $request, Response $response){
        $param = $request->getParsedBody();
        try{
            if(!empty($param["mesa"]) && isset($param["mesa"])){
                //entregar pedido cuando el estado sea listo para servir
                $bd = AccesoDatos::obtenerInstancia();
                $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'listo para servir' AND idMesa = :idMesa");
                $consulta->bindValue(":idMesa", $param["mesa"], PDO::PARAM_STR);
                $consulta->execute();
                $pedidosListos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                
                if(!empty($pedidosListos)){
                    foreach ($pedidosListos as $pedido){
                        if(!Pedido::verificarPedidosEntregados($param["mesa"])){
                            
                            // Actualizar el estado de la mesa asociada al pedido a "el cliente está comiendo"
                            Mesa::ActualizarEstadoMesa($pedido["idMesa"],"el cliente esta comiendo");
                            $estado = Pedido::ActualizarEstadoPedido("entregado",$pedido["codigoAlfa"]);
                            $response->getBody()->write(json_encode(["ESTADO"=>$estado]));
                        }else{
                            throw new Exception("todos los pedidos de la mesa deben esta listos para ser entregados");
                        }
                    }
                    $log = new registrador();
                    $log->registarActividad(" el mozo entrega el pedido y cambia el estado de la mesa a 'el cliente esta comiendo'"); 
                }else{
                    throw new Exception("no hay pedidos que entregar");
                }
            }else{
                throw new Exception("faltan parametros");
            }
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
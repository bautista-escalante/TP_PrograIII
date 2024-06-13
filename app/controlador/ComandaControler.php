<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Empleado.php";
class ComandaControler{

    public function atender(Request $request, Response $response){
        $files = $request->getUploadedFiles();
        $param = $request->getParsedBody();
        if(isset($param["pedido"],$param["nombre"],$files['foto'])&& !empty($files["foto"])){
            if ($files['foto']->getError() === UPLOAD_ERR_OK) {
                $dataFoto = file_get_contents($files['foto']->getStream()->getMetadata('uri'));
            } else {
                $dataFoto = null;
            }
            Empleado::atenderCliente($param["pedido"],$param["nombre"], $dataFoto);
            $response->getBody()->write("Cliente atendido");
            return $response->withStatus(200);
        }
        return $response->withStatus(400);
    } 
    public function cocinar(Request $request, Response $response){
            Empleado::atenderPedidos(obtenerUltimoId());
            return $response;
    }
    public function prepararTrago(Request $request, Response $response){
            Empleado::atenderPedidos(obtenerUltimoId());
            return $response;
    }
    public function servirCerveza(Request $request, Response $response){
            Empleado::atenderPedidos(obtenerUltimoId());
            return $response;
    }
}
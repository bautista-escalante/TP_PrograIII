<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "db/AccesoDatos.php";
include_once "modelo/Empleado.php";
class MozoControler{
    public function atender(Request $request, Response $response) {
        $files = $request->getUploadedFiles();
        $param = $request->getParsedBody();
        if (isset($param["pedido"], $param["nombre"])) {
            $dataFoto = null;
            if (isset($files['foto']) && $files['foto']->getError() === UPLOAD_ERR_OK) {
                $dataFoto = file_get_contents($files['foto']->getStream()->getMetadata('uri'));
            }
            Empleado::atenderCliente($param["pedido"], $param["nombre"], $dataFoto);
            $response->getBody()->write("Cliente atendido");
            return $response->withStatus(200);
        }
        $response->getBody()->write("Faltan datos del pedido o del cliente");
        return $response->withStatus(400);
    }
    
    public function servir(Request $request, Response $response){
        //entregar pedido cuando el estado sea listo para servir
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'listo para retirar'");
        $consulta->execute();
        $pedidosListos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if(empty($pedidosListos)){
            echo "no hay pedidos que entregar";
        }
        foreach ($pedidosListos as $pedido) {
            // Actualizar el estado de la mesa asociada al pedido a "el cliente está comiendo"
            $bd = AccesoDatos::obtenerInstancia();
            $update = $bd->prepararConsulta("UPDATE pedidos SET estado = 'servido' WHERE id = :id");
            $update->bindValue(":id", $pedido["id"],PDO::PARAM_INT);
            $update->execute();
            Mesa::ActualizarEstadoMesa($pedido["idMesa"],"el cliente esta comiendo");
            echo "El pedido con id " . $pedido["id"] . " ha sido entregado en la mesa ".$pedido["idMesa"]."<br>";
        }
        return $response;
    }
}
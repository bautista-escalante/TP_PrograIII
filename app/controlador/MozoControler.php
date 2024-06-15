<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "db/AccesoDatos.php";
include_once "modelo/Empleado.php";
class MozoControler{
    public function atender($pedido,$nombreCliente,Request $request, Response $response){
        $mozos = Empleado::obtenerEmpleadosPorPuesto("mozo"); 
        if(count($mozos) !=0){
            $i = rand(0,count($mozos)-1);
            $mozo = new Empleado($mozos[$i]["nombre"],$mozos[$i]["tipo"]);
            // el mozo atiende al cliente (asignar mesa) 
            $mozo->atenderCliente($pedido,$nombreCliente);
        }
        else{
            echo "no hay mozo disponible";
        }
        return $response;
    }
    public function servir(Request $request, Response $response){
    //entregar pedido cuando el estado sea listo para servir ( cambiar el estado de mesa a el cliente esta comiendo)
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'listo para servir'");
        $consulta->execute();
        $pedidosListos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if(count($pedidosListos) == 0){
            echo "no hay pedidos que entregar";
        }
        foreach ($pedidosListos as $pedido) {
            // Actualizar el estado de la mesa asociada al pedido a "el cliente está comiendo"
            Mesa::ActualizarEstadoMesa($pedido["idMesa"],"el cliente esta comiendo");
            echo "El pedido con ID " . $pedido["id"] . " ha sido entregado.<br>";
        }
        return $response;
    }
}
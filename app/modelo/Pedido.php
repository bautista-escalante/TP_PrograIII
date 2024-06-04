<?php
/* 
id - estado - idPedido(5valores alfanumericos) - tiempo - nombre - NombreEmpleadoEncargado

metodos -> calcularTiempo (genera un numeo ramdom de 1 a 60) para calcular segundos
        -> guardar
        -> actualizar estado();
*/
class Pedido {
    public $id;
    public $estado;
    public $idPedido;
    public $tiempo;
    public $NombreEmpleadoEncargado;
    public $nombrePedido;
    public $cancelado;
    public $cantidad;
    
    public function __construct($estasdo, $NombreEmpleadoEncargado, $nombre, $cantidad) {
        $this->id =null;
        $this->estado = $estasdo;
        $this->idPedido = $this->generarIdPedido();
        $this->tiempo = $this->calcularTiempo();
        $this->NombreEmpleadoEncargado = $NombreEmpleadoEncargado;
        $this->nombrePedido = $nombre;
        $this->cancelado = false;
        $this->cantidad = $cantidad;
    }
    private function generarIdPedido() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public function calcularTiempo() {
        return rand(1, 15);
    }
    private function verificar(){
        $db = AccesoDatos::obtenerInstancia();
        $consuta = $db->prepararConsulta("SELECT * FROM pedido");
        $pedidos = $consuta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        foreach($pedidos as $pedido){
            if($pedido->nombrePedido == $this->nombrePedido){
                return $pedido->idPedido;
            }
        }
        return true;
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $resultado = $this->verificar();
        if($resultado){
            $sql = "INSERT INTO pedido (estado, idPedido, tiempo, nombreCliente, NombreEmpleadoEncargado, cancelado, cantidad) 
            VALUES (:estado, :idPedido, :tiempo, :nombreCliente, :NombreEmpleadoEncargado, :cancelado, :cantidad)";
            $consulta = $bd->prepararConsulta($sql);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_INT);
            $consulta->bindValue(':nombreCliente', $this->nombrePedido, PDO::PARAM_STR);
            $consulta->bindValue(':NombreEmpleadoEncargado', $this->NombreEmpleadoEncargado, PDO::PARAM_STR);
            $consulta->bindValue(':cancelado', $this->cancelado, PDO::PARAM_BOOL);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->execute();
            $this->id = $bd->obtenerUltimoId();
        }
        else{
            $consulta = $bd->prepararConsulta("UPDATE pedido SET cantidad = :cantidad WHERE idPedido = :idPedido");
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
            $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
            $consulta->execute();
        }
}
    public function actualizarEstadoPedido($nuevoEstado) {
        $this->estado = $nuevoEstado;
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE pedido SET estado = :estado WHERE idPedido = :idPedido");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public function cancelar() {
        $this->cancelado = true;
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE pedido SET cancelado = :cancelado WHERE idPedido = :idPedido");
        $consulta->bindValue(':cancelado', $this->cancelado, PDO::PARAM_BOOL);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public function eliminarPedido(){
        if ($this->id !== null) {
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("DELETE FROM pedido WHERE id = :id");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->execute();
            echo "pedido eliminado de la base de datos.<br>";
        } else {
            echo "Error: El ID del pedido no es válido.<br>";
        } 
    }
}



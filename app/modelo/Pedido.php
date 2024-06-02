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
    
    public function __construct($estasdo, $NombreEmpleadoEncargado, $nombre) {
        $this->id =null;
        $this->estado = $estasdo;
        $this->idPedido = $this->generarIdPedido();
        $this->tiempo = $this->calcularTiempo();
        $this->NombreEmpleadoEncargado = $NombreEmpleadoEncargado;
        $this->nombrePedido = $nombre;
    }
    private function generarIdPedido() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public function calcularTiempo() {
        return rand(1, 15);
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO pedido (estado, idPedido, tiempo, NombreEmpleadoEncargado) VALUES (:estado, :idPedido, :tiempo, :NombreEmpleadoEncargado)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_INT);
        $consulta->bindValue(':NombreEmpleadoEncargado', $this->NombreEmpleadoEncargado, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public function actualizarEstadoPedido($nuevoEstado) {
        $this->estado = $nuevoEstado;
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE pedido SET estado = :estado WHERE idPedido = :idPedido");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
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



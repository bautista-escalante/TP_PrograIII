<?php
/*
    id(de 5 caracteres) - estado - puntuacion
    debe tener acceso a los tiempos  restanters para eso debo crear algun tipo de timer
    puntuacion
*/
class Mesa{
    public $id;
    public $idMesa;
    public $estado;
    public $puntuacion;

    public function __construct(){
        $this->id = null;
        $this->puntuacion = null;
        $this->estado = "con cliente esperando pedido";
        $this->idMesa = $this->generarIdMesa();
    }
    private function generarIdMesa() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO mesas (estado, puntuacion, idMesa) VALUES (:estado, :puntuacion, :idMesa)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacion', $this->puntuacion, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public function puntuarMesa($puntuacion){
        if($this->estado == "cerrada"){
            $this->puntuacion = $puntuacion;
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("UPDATE mesas SET puntuacion = :puntuacion WHERE id = :id");
            $consulta->bindValue(':puntuacion', $this->puntuacion, PDO::PARAM_STR);
            $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
            $consulta->execute();
        }
    }
    public function ActualizarEstadoMesa($nuevoEstado){
        $this->estado = $nuevoEstado;
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->execute();
    }
}
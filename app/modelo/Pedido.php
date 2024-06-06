<?php
/*
    una vez intanciado recojer el id para darselo al cliente
*/
include_once "db/AccesoDatos.php";
class Pedido{
    public $id; 
    public $idProducto; 
    public $codigoAlfa;
    public $idMesa; 
    public $idMozo; 
    public $idCocinero;
    public $tiempo;
    public $cancelado;

    public function __construct (){
        $this->id = null;
        $this->idProducto = null;
        $this->idMesa = null;
        $this->idMozo = null;
        $this->idCocinero = null;
        $this->cancelado = false;
        $this->codigoAlfa = $this->generarCodigo();
        $this->tiempo = rand(1, 15);
        $this->guardar();
    }
    private function generarCodigo() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    private function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO pedido (idProducto, codigoAlfa, idMesa, idMozo, idCocinero, tiempo, cancelado) 
                VALUES (:idProducto, :codigoAlfa, :idMesa, :idMozo, :idCocinero, :tiempo, :cancelado)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':codigoAlfa', $this->codigoAlfa, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idCocinero', $this->idCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_INT);
        $consulta->bindValue(':cancelado', $this->cancelado, PDO::PARAM_BOOL);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public function actualizarPedido($id, $idProducto=null, $idMesa=null, $idMozo=null, $idCocinero=null, $cancelado=false){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET idProducto = :idProducto, idMesa = :idMesa, idMozo = :idMozo, idCocinero = :idCocinero, cancelado = :cancelado WHERE id = :id");
        $update->bindValue(":id",$id,PDO::PARAM_INT);
        $update->bindValue(":idProducto",$idProducto,PDO::PARAM_INT);
        $update->bindValue(":idMesa",$idMesa,PDO::PARAM_INT);
        $update->bindValue(":idMozo",$idMozo,PDO::PARAM_INT);
        $update->bindValue(":idCocinero",$idCocinero,PDO::PARAM_INT);
        $update->bindValue(":cancelado",$cancelado,PDO::PARAM_BOOL);
    }
    public static function cancelarPedido($id){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET cancelado = :cancelado WHERE id = :id");
        $update->bindValue(":id",$id,PDO::PARAM_INT);
        $update->bindValue(":cancelado",true,PDO::PARAM_BOOL);
    }
    
}
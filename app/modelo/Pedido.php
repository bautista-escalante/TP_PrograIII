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
    public $foto;
    public $cancelado;
    public $estado;

    public function __construct (){
        $this->id = null;
        $this->idProducto = null;
        $this->idMesa = null;
        $this->idMozo = null;
        $this->foto = null;
        $this->idCocinero = null;
        $this->cancelado = false;
        $this->estado = "en preparacion";
        $this->codigoAlfa = $this->generarCodigo();
        $this->tiempo = rand(1, 15);
    }
    private function generarCodigo() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public static function ActualizarEstadoPedido($nuevoEstado, $id){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $update->bindValue(":id", $id, PDO::PARAM_INT);
        $update->bindValue(":estado", $nuevoEstado, PDO::PARAM_STR);
        $update->execute();
        echo("su pedido esta ".$nuevoEstado);
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO pedidos (idProducto, codigoAlfa, idMesa, idMozo, idCocinero, tiempo, cancelado, foto, estado) 
                VALUES (:idProducto, :codigoAlfa, :idMesa, :idMozo, :idCocinero, :tiempo, :cancelado, :foto, :estado)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':codigoAlfa', $this->codigoAlfa, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idCocinero', $this->idCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_INT);
        $consulta->bindValue(':cancelado', $this->cancelado, PDO::PARAM_BOOL);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_NULL);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public static function actualizarPedido($id, $idProducto=null, $idMesa=null, $idMozo=null, $idCocinero=null, $cancelado=false,$foto=null){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET idProducto = :idProducto, idMesa = :idMesa, idMozo = :idMozo, idCocinero = :idCocinero, cancelado = :cancelado, foto= :foto WHERE id = :id");
        $update->bindValue(":id",$id,PDO::PARAM_INT);
        $update->bindValue(":idProducto",$idProducto,PDO::PARAM_INT);
        $update->bindValue(":idMesa",$idMesa,PDO::PARAM_INT);
        $update->bindValue(":idMozo",$idMozo,PDO::PARAM_INT);
        $update->bindValue(":idCocinero",$idCocinero,PDO::PARAM_INT);
        $update->bindValue(":cancelado",$cancelado,PDO::PARAM_BOOL);
        $update->bindValue(":foto", $foto, $foto !== null ? PDO::PARAM_LOB : PDO::PARAM_NULL);
        $update->execute();
    }
    public static function cancelarPedido($id){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET cancelado = :cancelado WHERE id = :id");
        $update->bindValue(":id",$id,PDO::PARAM_INT);
        $update->bindValue(":cancelado",true,PDO::PARAM_BOOL);
    }
    public static function VerPrecio($idMesa){
        // obtener pedidos de la misma mesa
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT idProducto FROM pedidos WHERE idMesa = :idMesa");
        $select->bindValue(":idMesa",$idMesa, PDO::PARAM_INT);
        $select->execute();
        $productos = $select->fetchAll(PDO::FETCH_ASSOC);
        $precioFinal = 0;
        foreach($productos as $producto){
            //obtener el precio del producto individual
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT precio FROM producto WHERE id = :id");
            $select->bindValue(":id",$producto["idProducto"],PDO::PARAM_STR);
            $select->execute();
            $precio = $select->fetch(PDO::FETCH_ASSOC);
            $precioFinal += intval($precio['precio']);
        }
        var_dump($precioFinal);
        return $precioFinal;
    }
    public static function obtenerPedido($idMesa){
        if(!empty($idMesa)){
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE idMesa = :idMesa");
            $select->bindValue(":idMesa",$idMesa, PDO::PARAM_INT);
            $select->execute();
            return $select->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
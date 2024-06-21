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
    public $fechaInicio;
    public $fechaEntrega;

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
        $this->fechaInicio = date('Y-m-d H:i:s');
        $this->tiempo = null;
        $this->fechaEntrega = null;
    }
    public static function calcularTiempo($id){
        $tiempo = rand(1, 15); //segundos
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET tiempo = :tiempo WHERE id = :id");
        $update->bindValue(":id", $id, PDO::PARAM_INT);
        $update->bindValue(":tiempo", $tiempo, PDO::PARAM_STR);
        $update->execute();
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
        $sql = "INSERT INTO pedidos (idProducto, codigoAlfa, idMesa, idMozo, idCocinero, tiempo, cancelado, foto, estado, fechaInicio, fechaEntrega) 
                VALUES (:idProducto, :codigoAlfa, :idMesa, :idMozo, :idCocinero, :tiempo, :cancelado, :foto, :estado, :fechaInicio, :fechaEntrega)";
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
        $consulta->bindValue(':fechaInicio', $this->fechaInicio, PDO::PARAM_STR); 
        $consulta->bindValue(':fechaEntrega', $this->fechaEntrega, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public static function actualizarFechaEntrega($idPedido){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET fechaEntrega = :fechaEntrega WHERE id = :id");
        $update->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $update->bindValue(":fechaEntrega", date("Y/m/d H:i:s"),PDO::PARAM_STR);
        $update->execute();
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
        // mejorar esto para que no solo tome en cuenta laa mesa sino el pedido
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
    public static function estaATiempo($idPedido) {
        $db = AccesoDatos::obtenerInstancia();
        $select = $db->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $select->bindValue(":id", $idPedido, PDO::PARAM_INT);
        $select->execute();
        $pedido = $select->fetch(PDO::FETCH_ASSOC);
        if ($pedido) {
            $fechaInicio = strtotime($pedido["fechaInicio"]);
            $fechaEntrega = strtotime($pedido["fechaEntrega"]);
            $tiempo = intval($pedido["tiempo"]);
    
            if (($fechaEntrega - $fechaInicio) <= $tiempo) {
                return true;
            }
        }
        return false;
    }
    public static function GenerarEstadisticasPedido(){
        $db = AccesoDatos::obtenerInstancia();
        $fecha30DiasAtras = date("Y-m-d H:i:s", strtotime("-30 days"));
        $select = $db->prepararConsulta("SELECT id FROM pedidos WHERE fechaInicio > :fecha");
        $select->bindValue(":fecha", $fecha30DiasAtras, PDO::PARAM_STR);
        $select->execute();
        $ids = $select->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($ids) === 0) {
            return 0;
        }
        $ATiempo = 0;
        foreach($ids as $id){
            if(self::estaATiempo($id["id"])){
                $ATiempo ++;
            }
        }
        // probabilidad de que el tiempo se cumpla con el tiempo
        return $ATiempo/ count($ids); 
    }
}
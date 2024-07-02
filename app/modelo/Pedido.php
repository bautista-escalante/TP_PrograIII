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
    public $estado;
    public $fechaInicio;
    public $fechaEntrega;

    public function __construct (){
        $this->id = null;
        $this->idProducto = null;
        $this->idMesa = null;
        $this->idMozo = null;
        $this->idCocinero = null;
        $this->cancelado = false;
        $this->estado = "en preparacion";
        $this->codigoAlfa = null;
        $this->fechaInicio = date('Y-m-d H:i:s');
        $this->tiempo = null;
        $this->fechaEntrega = null;
    }
    public static function calcularTiempo($codigoAlfa){
        $tiempo = rand(60, 120); //segundos
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET tiempo = :tiempo WHERE codigoAlfa = :codigo");
        $update->bindValue(":codigo", $codigoAlfa, PDO::PARAM_INT);
        $update->bindValue(":tiempo", $tiempo, PDO::PARAM_STR);
        $update->execute();
    }
    public static function generarCodigo() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public static function ActualizarEstadoPedido($nuevoEstado, $codigoAlfa){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE codigoAlfa = :codigoAlfa");
        $update->bindValue(":codigoAlfa", $codigoAlfa, PDO::PARAM_INT);
        $update->bindValue(":estado", $nuevoEstado, PDO::PARAM_STR);
        $update->execute();
        return "su pedido esta ".$nuevoEstado;
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO pedidos (idProducto, codigoAlfa, idMesa, idMozo, idCocinero, tiempo, cancelado, estado, fechaInicio, fechaEntrega) 
                VALUES (:idProducto, :codigoAlfa, :idMesa, :idMozo, :idCocinero, :tiempo, :cancelado, :estado, :fechaInicio, :fechaEntrega)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':codigoAlfa', $this->codigoAlfa, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idMozo', $this->idMozo, PDO::PARAM_INT);
        $consulta->bindValue(':idCocinero', $this->idCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_NULL);
        $consulta->bindValue(':cancelado', $this->cancelado, PDO::PARAM_BOOL);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':fechaInicio', $this->fechaInicio, PDO::PARAM_STR); 
        $consulta->bindValue(':fechaEntrega', $this->fechaEntrega, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
        return $this->id;
    }
    public static function actualizarFechaEntrega($codigoAlfa){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET fechaEntrega = :fechaEntrega WHERE codigoAlfa = :codigoAlfa");
        $update->bindValue(':codigoAlfa', $codigoAlfa, PDO::PARAM_INT);
        $update->bindValue(":fechaEntrega", date("Y/m/d H:i:s"),PDO::PARAM_STR);
        $update->execute();
    }
    public static function actualizarEncargado($codigoAlfa, $idEncargado){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET idCocinero = :encargado WHERE codigoAlfa = :codigoAlfa");
        $update->bindValue(':codigoAlfa', $codigoAlfa, PDO::PARAM_INT);
        $update->bindValue(":encargado", $idEncargado,PDO::PARAM_STR);
        $update->execute();
    }
    public static function actualizarPedido($id, $codigoAlfa, $idProducto=null, $idMesa=null, $idMozo=null, $cancelado=false){
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE pedidos SET idProducto = :idProducto, codigoAlfa=:codigoAlfa, idMesa = :idMesa, idMozo = :idMozo, cancelado = :cancelado WHERE id = :id");
        $update->bindValue(":id",$id,PDO::PARAM_INT);
        $update->bindValue(":idProducto",$idProducto,PDO::PARAM_INT);
        $update->bindValue(":codigoAlfa",$codigoAlfa, PDO::PARAM_STR);
        $update->bindValue(":idMesa",$idMesa,PDO::PARAM_INT);
        $update->bindValue(":idMozo",$idMozo,PDO::PARAM_INT);
        $update->bindValue(":cancelado",$cancelado,PDO::PARAM_BOOL);
        $update->execute();
    }
    public static function cancelarPedido($CodigoAlfa){
        if(Pedido::VerificarCodigoAlfa($CodigoAlfa)){

            $bd = AccesoDatos::obtenerInstancia();
            $update = $bd->prepararConsulta("UPDATE pedidos SET cancelado = :cancelado, estado = 'cancelado' WHERE codigoAlfa = :codigo");
            $update->bindValue(":codigo",$CodigoAlfa,PDO::PARAM_INT);
            $update->bindValue(":cancelado",true,PDO::PARAM_BOOL);
            $update->execute();

            if ($update->rowCount() > 0) {
                $log = new Registrador();
                $log->registarActividad("el cliente cancelo el pedido".$CodigoAlfa);
                return "pedido eliminado.";
            } else {
                return "pedido no encontrado o ya eliminado.";
            }
        }else{
            return "el codigo alfa no existe";
        }
    }
    public static function obtenerPrecio($idMesa){
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
        return $precioFinal;
    }
    public static function obtenerPedido($alfa){
        if(!empty($alfa)){
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE codigoAlfa = :codigo");
            $select->bindValue(":codigo",$alfa, PDO::PARAM_STR);
            $select->execute();
            return $select->fetch(PDO::FETCH_ASSOC);
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
    public static function VerificarCodigoAlfa($codigoAlfa){
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE codigoAlfa = :codigoAlfa");
        $select->bindValue(":codigoAlfa", $codigoAlfa, PDO::PARAM_STR);
        $select->execute();
        if($select->fetchAll(PDO::FETCH_ASSOC) == null){
            return false;
        }else{
            return true;
        }
    }
    public static function verificarPedidosEntregados($IdMesa){
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE idMesa = :idMesa");
            $select->bindValue(":idMesa", $IdMesa, PDO::PARAM_STR);
            $select->execute();
            $pedidos = $select->fetchAll(PDO::FETCH_ASSOC);

            $falta = false;
            foreach($pedidos as $pedido){
                if($pedido["estado"] === 'en preparacion'){
                    $falta = true;
                    break;
                }
            }
            return $falta;
    }
    public static function tiempoDemora($numPedido, $NumMesa){
        $db = AccesoDatos::obtenerInstancia();
        $select = $db->prepararConsulta("SELECT tiempo FROM pedidos WHERE codigoAlfa = :codigo AND idMesa = :idMesa");
        $select->bindValue(":codigo", $numPedido, PDO::PARAM_STR);
        $select->bindValue(":idMesa", $NumMesa, PDO::PARAM_STR);
        $select->execute();
        $tiempo = $select->fetch(PDO::FETCH_ASSOC);

        if(!empty($tiempo)){
            return $tiempo;
        }else{
            return "pedido no encontrado";
        }
    }
    public static function guardarPuntuacion($puntuacionMozo, $comentarioMozo, $puntuacionCocinero, $comentarioCocinero, $calificacionMesa, $comentarioMesa) {
            $db = AccesoDatos::obtenerInstancia();
            $consulta = "INSERT INTO comentarios (puntuacionMozo, comentarioMozo, puntuacionCocinero, comentarioCocinero, puntuacionMesa, comentarioMesa) 
                    VALUES (:puntuacionMozo, :comentarioMozo, :puntuacionCocinero, :comentarioCocinero, :puntuacionMesa, :comentarioMesa)";
            
            $insert = $db->prepararConsulta($consulta);
            $insert->bindValue(':puntuacionMozo', $puntuacionMozo, PDO::PARAM_INT);
            $insert->bindValue(':comentarioMozo', $comentarioMozo, PDO::PARAM_STR);
            $insert->bindValue(':puntuacionCocinero', $puntuacionCocinero, PDO::PARAM_INT);
            $insert->bindValue(':comentarioCocinero', $comentarioCocinero, PDO::PARAM_STR);
            $insert->bindValue(':puntuacionMesa', $calificacionMesa, PDO::PARAM_INT);
            $insert->bindValue(':comentarioMesa', $comentarioMesa, PDO::PARAM_STR);
            $insert->execute();
    }
    public static function verMejorcomentario(){
        $db = AccesoDatos::obtenerInstancia();
        $selectMozo = $db->prepararConsulta("SELECT comentarioMozo FROM comentarios ORDER BY puntuacionMozo DESC LIMIT 1");
        $selectMozo->execute();
        $comentarioMozoAlto = $selectMozo->fetch(PDO::FETCH_ASSOC);

        $db = AccesoDatos::obtenerInstancia();
        $selectMesa = $db->prepararConsulta("SELECT comentarioMesa FROM comentarios ORDER BY puntuacionMozo DESC LIMIT 1");
        $selectMesa->execute();
        $comentarioMesaAlto = $selectMesa->fetch(PDO::FETCH_ASSOC);
        
        $db = AccesoDatos::obtenerInstancia();
        $selectCocinero = $db->prepararConsulta("SELECT comentarioCocinero FROM comentarios ORDER BY puntuacionMozo DESC LIMIT 1");
        $selectCocinero->execute();
        $comentarioCocineroAlto = $selectCocinero->fetch(PDO::FETCH_ASSOC);

        $db = AccesoDatos::obtenerInstancia();
        $selectCocinero = $db->prepararConsulta("SELECT comentarioResto FROM comentarios ORDER BY puntuacionResto DESC LIMIT 1");
        $selectCocinero->execute();
        $comentarioRestoAlto = $selectCocinero->fetch(PDO::FETCH_ASSOC);

        if ($comentarioMozoAlto["comentarioMozo"] && $comentarioMesaAlto["comentarioMesa"] && $comentarioCocineroAlto["comentarioCocinero"]) {
            return array("mozo" => $comentarioMozoAlto["comentarioMozo"],
                    "mesa" => $comentarioMesaAlto["comentarioMesa"],
                    "cocinero" => $comentarioCocineroAlto["comentarioCocinero"],
                    "la comanda"=>$comentarioRestoAlto["comentarioResto"]);
        } else {
            throw new Exception("no hay comentarios");
        }
    }
    public static function verMesaMasUsada(){
        $db = AccesoDatos::obtenerInstancia();
        $select = $db->prepararConsulta("SELECT idMesa, COUNT(*) AS cantidad FROM pedidos GROUP BY idMesa ORDER BY cantidad DESC LIMIT 1;");
        $select->execute();
        $mesa = $select->fetch(PDO::FETCH_ASSOC);
        
        if ($mesa){
            return $mesa;
        } else {
            throw new Exception("No se encontraron resultados.");
        }
    }
    public static function listarPedidosPendientes($idEmpleado){

        $empleado = Empleado::ObtenerEmpleado($idEmpleado);
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'en preparacion'");
        $select->execute();
        $pedidos = $select->fetchAll(PDO::FETCH_ASSOC);

        if($pedidos){
            $pendientes = [];
            foreach($pedidos as $pedido){
                $producto = Producto::obtenerProducto($pedido["idProducto"]);
                if($producto["puestoResponsable"] === $empleado["tipo"]){
                    $pendientes [] = $pedido["codigoAlfa"];
                }
            }
        }
        if(!empty($pendientes)){
            return $pendientes;
        }
        throw new Exception("no hay pedidos pendientes");
    }
}
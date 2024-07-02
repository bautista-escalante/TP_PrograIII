<?php
include_once "db/AccesoDatos.php";

class Cliente {
    public $nombre;
    public $idPedido;
    public $numPedido;
    public $foto;
    public $fechaBaja;
    public function __construct($nombre, $numPedido, $foto, $idPedido) {
        $this->nombre = $nombre;
        $this->numPedido = $numPedido;
        $this->foto = $foto;
        $this->idPedido = $idPedido;
    } 
    public function mostrarTiempoRestante() {
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'en preparacion'");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        
        foreach ($pedidos as $pedido){
            if (time() - $pedido->timestamp >= $pedido->tiempo) {
                echo "El tiempo restante para el pedido $this->numPedido es de 00:$pedido->tiempo <br>";
            }
        }
    }
    public static function calificar($idMesa, $idCocinero, $idMozo, $codigoAlfa, $calificacionMesa, $comentarioMesa, $calificacionCocinero, $comentarioCocinero, $calificacionMozo, $comentarioMozo, $calificacionResto, $comentarioResto) {
        if(intval($calificacionCocinero) > 5 || intval($calificacionCocinero) < 0 ||
        intval($calificacionMesa) > 5 || intval($calificacionMesa) < 0 ||
        intval($calificacionMozo) > 5 || intval($calificacionMozo) < 0 ||
        intval($calificacionResto) > 5 || intval($calificacionResto) < 0 ){
            throw new Exception("las calificaiones deben ser mayores a cero pero menores o iguales a cinco");
        }
        if (strlen($comentarioCocinero) > 66 || strlen($comentarioMozo) > 66 || 
        strlen($comentarioMesa) > 66 || strlen($comentarioResto) > 66) {
            throw new Exception("Excediste la cantidad máxima de caracteres en uno o más comentarios. Refactoriza tus comentarios para que ocupen menos de 66 caracteres cada uno.");
        } 

        $db = AccesoDatos::obtenerInstancia();
        $insert = $db->prepararConsulta("INSERT INTO comentarios (puntuacionMozo, comentarioMozo, puntuacionCocinero, comentarioCocinero, puntuacionMesa, comentarioMesa, puntuacionResto, comentarioResto) 
                                          VALUES (:puntuacionMozo, :comentarioMozo, :puntuacionCocinero, :comentarioCocinero, :puntuacionMesa, :comentarioMesa, :puntuacionResto, :comentarioResto)");

        $insert->bindValue(":puntuacionMozo", $calificacionMozo, PDO::PARAM_INT);
        $insert->bindValue(":comentarioMozo", $comentarioMozo, PDO::PARAM_STR);
        $insert->bindValue(":puntuacionCocinero", $calificacionCocinero, PDO::PARAM_INT);
        $insert->bindValue(":comentarioCocinero", $comentarioCocinero, PDO::PARAM_STR);
        $insert->bindValue(":puntuacionMesa", $calificacionMesa, PDO::PARAM_INT);
        $insert->bindValue(":comentarioMesa", $comentarioMesa, PDO::PARAM_STR);
        $insert->bindValue(":puntuacionResto", $calificacionResto, PDO::PARAM_INT);
        $insert->bindValue(":comentarioResto", $comentarioResto, PDO::PARAM_STR);
    
        $insert->execute();
        
        Mesa::CalificarMesa($idMesa, $calificacionMesa);
        Empleado::calificarEmpleado($idCocinero, $calificacionCocinero, "cocinero");
        Empleado::calificarEmpleado($idMozo, $calificacionMozo, "mozo");
    }
}

//

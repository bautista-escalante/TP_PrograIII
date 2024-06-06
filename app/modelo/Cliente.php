<?php
/*
nombre - mesa - numPedido - foto - idMesa

metodos -> MostrartiempoRestante(segun el numpedido e idmesa)
        -> calificar
*/

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
        
        foreach ($pedidos as $pedido) {
            if (time() - $pedido->timestamp >= $pedido->tiempo) {
                echo "El tiempo restante para el pedido $this->numPedido es de 00:$pedido->tiempo <br>";
            }
        }
    }
    public function calificar($calificacionMesa,$calificacionCocinero,$calificacionMozo ) {
        /* Mesa::CalificarMesa($calificacionMesa);
        Empleado::calificarCocinero($calificacionCocinero,$this->idCocinero);
        Empleado::calificarMozo($calificacionMozo,$this->idMozo); */
    }
    public function actualizarOperacion($mesa, $nombreMozo){
        $archivo = file_get_contents("Operaciones.json");
        $operaciones = json_decode($archivo);
        foreach($operaciones as &$operacion){
            $operacion["mesa"] = $mesa;
            $operacion["nombreMozo"] = $nombreMozo;
        } 
        file_put_contents("Operaciones.json",json_encode($operaciones,true,JSON_PRETTY_PRINT));
    }
}

//

<?php
/*
nombre - mesa - numPedido - foto - idMesa

metodos -> MostrartiempoRestante(segun el numpedido e idmesa)
        -> calificar
*/

class Cliente {
    public $nombre;
    public $mesa;
    public $numPedido;
    public $foto;
    public $idMesa;
    public function __construct($nombre, $mesa, $numPedido, $foto, $idMesa) {
        $this->nombre = $nombre;
        $this->mesa = $mesa;
        $this->numPedido = $numPedido;
        $this->foto = $foto;
        $this->idMesa = $idMesa;
    } 
    public function mostrarTiempoRestante() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "SELECT * FROM pedidos WHERE estado = 'en preparacion'";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        
        foreach ($pedidos as $pedido) {
            if (time() - $pedido->timestamp >= $pedido->tiempo) {
                echo "El tiempo restante para el pedido $this->numPedido es de 00:$pedido->tiempo <br>";
            }
        }
    }
    public function calificar($calificacion) {
        // llamar a todos los metodos de calificar y guardar en json los resultados e imprimir los promedios
    }
}

//

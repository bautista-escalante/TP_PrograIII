<?php
/* 
pendientes - id - nombre - tipo(mozo-bartender-cocinero-cervecero) - estadoEmpleado(true-false segun este ocupado)

metodos -> si no es mozo atenderPedidos (cambiar estados del pedido dependiendo del id del producto en preparación)tiempo estimado de finalización
        pasado el tiempo e estado debe ser “listo para servir”
        -> rotarPersonal (actualiza el tipo segun se pasa por parametros)
        -> si es mozo atenderCliente (dar el id al cliente y crear la intancia)
        -> si es mozo tomarPedido (repartir el pedido a los empreados correspondientes)
        -> puntuar(puntucacion);
        -> si es mozo cambiarEstadoMesa(con cliente esperando pedido ,”con cliente comiendo”,
        “con cliente pagando”)
*/
class Empleado{
    public $id;
    public $nombre;
    public $pendientes;
    public $tipo;
    public $ocupado;

    public function __construct($nombre, $pendiente, $tipo)
    {
        $datos = AccesoDatos::obtenerInstancia();
        $this->id = $datos->obtenerUltimoId();
        $this->nombre = $nombre;
        $this->pendientes = $pendiente;
        $this->tipo = $tipo;
        $this->ocupado = false;
    } 
    public function atenderPedidos(){

    }
    public function rotarPersonal(){
        
    }

}
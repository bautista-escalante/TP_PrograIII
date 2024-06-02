<?php
/* 
pendientes - id - nombre - tipo(mozo-bartender-cocinero-cervecero) - estadoEmpleado(true-false segun este ocupado)

metodos -> si no es mozo atenderPedidos (cambiar estados del pedido dependiendo del id del producto 
        en preparación)tiempo estimado de finalización
        pasado el tiempo e estado debe ser “listo para servir”
        -> rotarPersonal (actualiza el tipo segun se pasa por parametros)

        -> si es mozo atenderCliente (dar el id al cliente y crear la intancia)
        -> si es mozo tomarPedido (repartir el pedido a los empreados correspondientes)
        -> si es mozo cambiarEstadoMesa(con cliente esperando pedido ,”con cliente comiendo”,
        “con cliente pagando”)
*/
class Empleado{
    public $id;
    public $nombre;
    public $pendientes = [];
    public $tipo;
    public $ocupado;
    public function __construct($nombre, $pendiente, $tipo)
    {
        $this->id = null;
        $this->nombre = $nombre;
        $this->pendientes = $pendiente;
        $this->tipo = $tipo;
        $this->ocupado = false;
    } 
    public function atenderPedidos(){
        // en empleadocontroler debo dividir segun el tipo
        if($this->tipo != "mozo"){
            $this->actualizarEstadoEmpleado(true);
            echo "tu pedido esta a cargo de ". $this->nombre ."<br>";
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'en preparacion'");
            $consulta->execute();
            $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
            foreach ($pedidos as $pedido) {
                if (time() - $pedido->timestamp >= $pedido->tiempo){
                    $pedido->actualizarEstadoPedido("listo para servir");
                    $this->actualizarEstadoEmpleado(false);
                    echo "tu pedido esta listo<br>";
                }
                echo "tu pedido todabia esta en preparacion";
            }
        }
        else{
            echo "error. al mozo no le corresponde esta tarea<br>";
        }
    }
    public function servir(){
        //entregar pedido cuando el estado sea listo para servir ( cambiar el estado de mesa a el cliente esta comiendo)
        if($this->tipo == "mozo"){
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("SELECT * FROM pedido WHERE estado = 'listo para servir'");
            $consulta->execute();
            $pedidosListos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    
            foreach ($pedidosListos as $pedido) {
                // Actualizar el estado de la mesa asociada al pedido a "el cliente está comiendo"
                $consultaMesa = $bd->prepararConsulta("UPDATE mesas SET estado = 'el cliente está comiendo' WHERE id = :idMesa");
                $consultaMesa->bindValue(':idMesa', $pedido->idMesa, PDO::PARAM_INT);
                $consultaMesa->execute();
                // cambiar estado del a mesa
                echo "El pedido con ID " . $pedido->id . " ha sido entregado y la mesa .<br>";
            }
        } else {
            echo "error. esta tarea debe ser realizada unicamente por el mozo.<br>";
        }
    }
    public function guardar() { 
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO empleados (nombre, pendientes, tipo, ocupado)
                VALUES (:nombre, :pendientes, :tipo, :ocupado)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':pendientes', $this->pendientes, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_INT);
        $consulta->bindValue(':ocupado', $this->ocupado, PDO::PARAM_BOOL);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public function actualizarEstadoEmpleado($nuevoEstado) {
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE empleados SET ocupado = :ocupado WHERE id = :id");
        $consulta->bindValue(':ocupado', $nuevoEstado, PDO::PARAM_BOOL);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public function rotarPersonal($nuevoPuesto){
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "UPDATE empleados SET tipo = :tipo WHERE id = :id";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':tipo', $nuevoPuesto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }
    // este metodo debe estar en socio.p´hp
    public function despedir() {
        if ($this->id !== null) {
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("DELETE FROM empleados WHERE id = :id");
            $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->execute();
            echo "Empleado despedido y eliminado de la base de datos.<br>";
        } else {
            echo "Error: El ID del empleado no es válido.<br>";
        } 
    }
    public function atenderCliente($pedido){
        if($this->tipo == "mozo"){
            $pedido = new Pedido("en preparacion",$this->nombre, $pedido);
            $pedido->guardar();
            //asignar mesa 
            $pedido->idPedido;
            // invocar cliente y pasado el estado cambiar a cliente comiendo (mesa) 
        }
        else{
            echo "esta tarea debe ser realizada unicamente por el mozo";
        }
    }
}
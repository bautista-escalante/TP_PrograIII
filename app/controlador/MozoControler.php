<?php
/* 
el mozo debe encargarse de atender a los clientes esto con lleva
    - asignarles una mesa disponible
    - tomar los pedidos y encargarselo a los empleados
    - darle al cliente el id del pedido
*/
include_once "db/AccesoDatos.php";
include_once "modelo/Empleado.php";
function atender($pedido){
    $empleados = Empleado::obtenerEmpleadosPorPuesto("mozo");
    if(count($empleados) !=0){
        $i = rand(0,count($empleados)-1);
        $mozo = new Empleado($empleados[$i]["nombre"],$empleados[$i]["tipo"]);
        $mozo->atenderCliente($pedido);
    }
    else{
        echo "no hay mozo disponible";
    }
}
function servir(){
    //entregar pedido cuando el estado sea listo para servir ( cambiar el estado de mesa a el cliente esta comiendo)
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("SELECT * FROM pedido WHERE estado = 'listo para servir'");
        $consulta->execute();
        $pedidosListos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        if(count($pedidosListos) == 0){
            echo "no hay pedidos que entregar";
        }
        foreach ($pedidosListos as $pedido) {
            // Actualizar el estado de la mesa asociada al pedido a "el cliente está comiendo"
            Mesa::ActualizarEstadoMesa("el cliente esta comiendo");
            echo "El pedido con ID " . $pedido->id . " ha sido entregado.<br>";
            sleep(15);
            Mesa::ActualizarEstadoMesa("con cliente pagando");
            echo "el cliente pago <br>";
        }
}
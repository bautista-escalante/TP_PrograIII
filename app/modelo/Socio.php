<?php
/*
-> agreagarPersonal
-> suspenderPersonal
-> despedir
-> ver estado del pedido
-> cerrarMesa
-> VerProductoMasVendido
-> VerProductoMenosVendido
-> verCancelados

metodos
        -> verHorariosEmpleados
        -> VerPedidosFueraHorario
        -> verOperaciones()
        -> OperacionPorEmpleado

hoy deberia terminar esta clase y empezar con controlador 

*/
class Socio{
        public static function VerEstadoPedido($id){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT estado FROM pedido WHERE id = :id");
                $consulta->bindValue(":id",$id, PDO::PARAM_INT);
                $consulta->execute();
        }
        public static function cerrarMesa(){
                Mesa::ActualizarEstadoMesa("cerrada");
        }
        public static function VerHorariosEmpleados(){
                
        }
        public static function verOperaciones($tipo){
                $archivo = file_get_contents("Operaciones.json");
                $datos = json_decode($archivo,true);
                foreach($datos as $operacion){
                        switch($tipo){
                                case "bartender":
                                        echo("el pedido de".$operacion["cantidad"].$operacion["nombrePedido"]." esta a cargo de ".$operacion["nombreEmpleado"].
                                        " y tardara ".$operacion["tiempo"]." segundos<br>");
                                break;
                                case "cocinero":
                                        echo("el pedido de".$operacion["cantidad"].$operacion["nombrePedido"]." esta a cargo de ".$operacion["nombreEmpleado"].
                                        " y tardara ".$operacion["tiempo"]." segundos<br>");
                                break;
                                case "cervecero":
                                        echo("el pedido de".$operacion["cantidad"].$operacion["nombrePedido"]." esta a cargo de ".$operacion["nombreEmpleado"].
                                        " y tardara ".$operacion["tiempo"]." segundos<br>");
                                break;
                        }
                }
        }
        public static function VerProductoMasVendido(){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT MAX(cantidad) AS cantidadMaxima FROM pedido");
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                $maximo = $resultado['cantidadMaxima'];
                $consulta = $db->prepararConsulta("SELECT nombrePedido FROM pedido WHERE cantidad = :cantidad");
                $consulta->bindValue(":cantidad",$maximo,PDO::PARAM_INT);
                $consulta->execute();
                $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
                foreach($pedidos as $pedido){
                        echo("el producto mas vendido es: ".$pedido->nombrePedido."<br>");
                }
        }
        public static function VerProductoMenosVendido(){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT MIN(cantidad) AS cantidadMinima FROM pedido");
                $consulta->execute();
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
                $minimo = $resultado['cantidadMinima'];
                $consulta = $db->prepararConsulta("SELECT nombrePedido FROM pedido WHERE cantidad = :cantidad");
                $consulta->bindValue(":cantidad",$minimo,PDO::PARAM_INT);
                $consulta->execute();
                $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
                foreach($pedidos as $pedido){
                        echo("el producto menos vendido es: ".$pedido->nombrePedido."<br>");
                }
        }
        public static function VerPedidosFueraHorario(){

        }
        public static function VerCancelados(){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT * FROM pedido WHERE cancelado = :cancelado");
                $consulta->bindValue(":cancelado",true,PDO::PARAM_BOOL);
                $consulta->execute();
                $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
                if(count($pedidos) != 0){
                        echo "estos son los pedidos cancelados: <br>";
                        foreach($pedidos as $pedido){
                                echo("pedido de ". $pedido->tipo."<br>".
                                "a cargo de: ". $pedido->NombreEmpleadoEncargado);
                        }
                }else{
                        echo "no hay pedidos cancelados";
                }
        }
        public static function contratarEmpleado($nombre, $tipo) {
                $empleado = new Empleado($nombre, $tipo);
                $empleado->guardar();
                echo "$nombre fue contrata para trabajar de $tipo <br>";
        }
        public static function suspenderEmpleado(Empleado $empleado) {
                $empleado->actualizarEstadoEmpleado(false);
                echo "el empleado ".$empleado->nombre. "fue suspendido<br>";
        }
        public static function despedirEmpleado($id) {
                if ($id !== null) {
                    $bd = AccesoDatos::obtenerInstancia();
                    $consulta = $bd->prepararConsulta("DELETE FROM empleados WHERE id = :id");
                    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                    $consulta->execute();
                    echo "Empleado despedido y eliminado de la base de datos.<br>";
                } else {
                    echo "Error: El ID del empleado no es válido.<br>";
                } 
        }
}
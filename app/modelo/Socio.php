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
        -> verMesaMasUsada()
        -> verMesaMenosUsada()
        -> vermajorFactura()
        -> vermenorFactura()->resultados finales
        -> verMajorImporte()
        -> vermenorImporte()
        -> facturacionEntreFechas()
        -> verMejorComentario()
        -> verpeorComentario()
        -> verHorariosEmpleados
        -> VerPedidosFueraHorario
        -> verOperaciones()
        -> OperacionPorEmpleado

hoy deberia terminar esta clase y empezar con controlador 

*/
include_once "db\AccesoDatos.php";
include_once "Empleado.php";
include_once "Pedido.php";
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
                $archivo = file_get_contents("modelo/Operaciones.json");
                $datos = json_decode($archivo,true);
                $encontrado = false;
                foreach($datos as $operacion){
                        if($operacion["puesto"] == $tipo){
                                echo("el pedido de ".$operacion["cantidad"]." ".$operacion["nombrePedido"]." esta a cargo de ".$operacion["nombreEmpleado"].
                                " y tardara ".$operacion["tiempo"]." segundos<br>");
                                $encontrado = true;
                        }
                }
                if(!$encontrado){
                        echo "no hay peidos de ese rubro";
                }
        }
        public static function VerProductoMasVendido(){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT nombreCliente, SUM(Cantidad) as cantidadTotal FROM pedido GROUP BY nombreCliente");
                $consulta->execute();
                $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);

                $cantidades = [];
                foreach ($pedidos as $pedido) {
                $nombreProducto = $pedido["nombreCliente"];
                $cantidades[$nombreProducto] = $pedido["cantidadTotal"];
                }
                $productoMenosVendido = array_keys($cantidades, max($cantidades))[0];
                echo "El producto menos vendido es: {$productoMenosVendido} con una cantidad de: {$cantidades[$productoMenosVendido]}<br>";
        }
        public static function VerProductoMenosVendido(){
                $db = AccesoDatos::obtenerInstancia();
                $consulta = $db->prepararConsulta("SELECT nombreCliente, SUM(Cantidad) as cantidadTotal FROM pedido GROUP BY nombreCliente");
                $consulta->execute();
                $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                $cantidades = [];
                foreach ($pedidos as $pedido) {                            
                        $nombreProducto = $pedido["nombreCliente"];
                        $cantidades[$nombreProducto] = $pedido["cantidadTotal"];
                }
                $productoMenosVendido = array_keys($cantidades, min($cantidades))[0];
                echo "El producto menos vendido es: {$productoMenosVendido} con una cantidad de: {$cantidades[$productoMenosVendido]}<br>";              
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
                                return "pedido de ". $pedido->tipo."<br>".
                                "a cargo de: ". $pedido->NombreEmpleadoEncargado;
                        }
                }else{
                        return "no hay pedidos cancelados";
                }
        }
        public static function contratarEmpleado($nombre, $tipo) {
                $empleado = new Empleado($nombre, $tipo);
                $empleado->guardar();
                echo "$nombre fue contrata para trabajar de $tipo <br>";
        }
        public static function suspenderEmpleado($id) {
                $bd = AccesoDatos::obtenerInstancia();
                $consulta = $bd->prepararConsulta("UPDATE empleados SET ocupado = :ocupado WHERE id = :id");
                $consulta->bindValue(':ocupado', true, PDO::PARAM_BOOL);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
                echo "el empleado fue suspendido<br>";
        }
        public static function despedirEmpleado($id) {
                try{
                        if ($id !== null) {
                                $db = AccesoDatos::obtenerInstancia();
                                $update = $db->prepararConsulta("UPDATE empleados SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
                                $update->bindValue(":id", $id, PDO::PARAM_INT);
                                $update->execute();                        
                                if ($update->rowCount() > 0) {
                                        echo "Empleado eliminado.";
                                } else {
                                        echo "Empleado no encontrado o ya eliminado.";
                                }
                                echo "Empleado despedido.<br>";
                        } else {
                                echo "Error: El ID del empleado no es válido.<br>";
                        } 
                } catch (PDOException $e) {
                        echo "Error en la base de datos";
                }
        }
}
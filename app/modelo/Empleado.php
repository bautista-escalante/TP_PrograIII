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
include_once "Pedido.php";
include_once "Mesa.php";
include_once "Empleado.php";
include_once "Producto.php";
class Empleado{
    public $id;
    public $nombre;
    public $tipo;
    public $ocupado;
    public function __construct($nombre, $tipo)
    {
        $this->id = null;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->ocupado = false;
    }
    public static function obtenerPedidos($id){
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE idCocinero = :id AND estado = 'en preparacion'");
        $select->bindValue(":id", $id, PDO::PARAM_INT);
        $select->execute();
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }
    // deberia obtener este id del json de registro, segun quien ingreso la ultima vez es quier tiene el pedido
    // verificar usarndo mw que el usuario  no sea mozo ni socio
    public static function atenderPedidos($idEmpleado){
        Empleado::actualizarEstadoEmpleado(true,$idEmpleado);
        $pedidos = Empleado::obtenerPedidos($idEmpleado);
        if(!empty($pedidos)){
            foreach($pedidos as $pedido){
                Pedido::ActualizarEstadoPedido("listo para retirar",$pedido["id"]);
            }
        }
        else{
            echo "no hay pedidos pendientes";
        }
    }
    public static function calificarMozo($idMozo, $calificacion){
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT puntuacion FROM empleados WHERE id = :id AND tipo = mozo");
        $select->bindParam(':id', $idMozo, PDO::PARAM_STR);
        $select->execute();
        $result = $select->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['puntuacion'])){
            $puntuaciones = json_decode($result['puntuacion'], true);
            $puntuaciones[] = $calificacion;
            $promedio = array_sum($puntuaciones) / count($puntuaciones);
        } else {
            $promedio = $calificacion;    
        }
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE empleados SET puntuacion = :puntuacion WHERE id = :id AND tipo = mozo");
        $update->bindParam(':puntuacion', $promedio, PDO::PARAM_STR);
        $update->bindParam(':id', $idMozo, PDO::PARAM_INT);
        $update->execute();
    }
    public static function calificarCocinero($idCocinero, $calificacion){
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT puntuacion FROM empleados WHERE id = :id AND tipo = cocinero");
        $select->bindParam(':id', $idCocinero, PDO::PARAM_STR);
        $select->execute();
        $result = $select->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['puntuacion'])) {
            $puntuaciones = json_decode($result['puntuacion'], true);
            $puntuaciones[] = $calificacion;
            $promedio = array_sum($puntuaciones) / count($puntuaciones);
        } else {
            $promedio = $calificacion;    
        }
        $bd = AccesoDatos::obtenerInstancia();
        $update = $bd->prepararConsulta("UPDATE empleados SET puntuacion = :puntuacion WHERE id = :id AND tipo = cocinero");
        $update->bindParam(':puntuacion', $promedio, PDO::PARAM_STR);
        $update->bindParam(':id', $idCocinero, PDO::PARAM_INT);
        $update->execute();
    }
    public function guardar() { 
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO empleados (nombre, pendientes, tipo, ocupado)
                VALUES (:nombre, :tipo, :ocupado)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_INT);
        $consulta->bindValue(':ocupado', $this->ocupado, PDO::PARAM_BOOL);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public static function actualizarEstadoEmpleado($nuevoEstado, $id) {
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE empleados SET ocupado = :ocupado WHERE id = :id");
        $consulta->bindValue(':ocupado', $nuevoEstado, PDO::PARAM_BOOL);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
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
    public static function atenderCliente($pedido,$nombre,$foto=null){
        // consigo el puesto segun la comida ej: "ferenet con coca -> bartender"
        $encargado = Empleado::obtenerEncargado($pedido);
        // obtengo todos los empleados que puede ejecutar el pedido
        $empleadosEncontrados = Empleado::obtenerEmpleadosPorPuesto($encargado);
        $i = array_rand($empleadosEncontrados);
        $empleadoEncontrado = $empleadosEncontrados[$i];
        if($empleadoEncontrado != false){
            $dataProducto = Producto::obtenerProducto($pedido);
            $mozos = Empleado::obtenerEmpleadosPorPuesto("mozo");
            $i = array_rand($mozos);
            $mozo = $mozos[$i];
            echo ("mi nombre es ".$mozo["nombre"]. " y sere su mozo esta noche<br>");
            $mesa = Mesa::AsignarMesa();
            Mesa::ActualizarEstadoMesa($mesa,"con cliente esperando pedido");
            echo($mozo["nombre"]." le asigna la mesa ".$mesa." al cliente ".$nombre."<br>");
            echo("el pedido esta a cargo de ".$empleadoEncontrado["nombre"]." <br>");
            $encargo = new Pedido();
            $encargo->guardar();
            //idProducto, idMesa; idMozo; idCocinero; cancelado; foto
            Pedido::actualizarPedido($encargo->id,$dataProducto["id"], $mesa, $mozo["id"],$empleadoEncontrado["id"],false,$foto);
        }else{
            echo "no hay empleados que raro no?<br>";
        }
    }
    public static function obtenerEmpleadosPorPuesto($puesto){
            try {
                $bd = AccesoDatos::obtenerInstancia();
                $query = "SELECT * FROM empleados WHERE tipo = :puesto AND deleted_at IS NULL";
                $select = $bd->prepararConsulta($query);
                $select->bindParam(':puesto', $puesto, PDO::PARAM_STR);
                $select->execute();
                return $select->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
    }
    private static function obtenerEncargado($producto){
        $producto = Producto::buscarProducto($producto);
        if($producto != false){
            return $producto["puestoResponsable"];
        }
        else{
            echo "error no tenemos esa comida en nuestro menu";
        }
    }
}
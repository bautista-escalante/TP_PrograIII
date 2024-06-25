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
    private static function obtenerPedidos($id){
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE idCocinero = :id AND estado = 'en preparacion'");
        $select->bindValue(":id", $id, PDO::PARAM_INT);
        $select->execute();
        $pedidos = $select->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($pedidos)){
            foreach($pedidos as $pedido){
                Pedido::calcularTiempo($pedido["id"]);
            }
            return $pedidos;
        }else{
            throw new Exception("no hay pedidos que preparar");
        }
    }
    public static function atenderPedidos($idEmpleado){
        // obtengo el usuario que esta haciendo la operacion en base al jwt 
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $select->bindValue(":id", $idEmpleado, PDO::PARAM_INT);
        $select->execute();
        $usuario = $select->fetch(PDO::FETCH_ASSOC);

        $nombre = $usuario["usuario"];

        Empleado::actualizarEstadoEmpleado(true,$idEmpleado);
        //obtengo los pedidos pendientes
        $pedidos = Empleado::obtenerPedidos($idEmpleado);

        if(!empty($pedidos) && !empty($nombre)){
            $log = new registrador();
            foreach($pedidos as $pedido){
                // el empleado le asiga el tiempo
                Pedido::calcularTiempo($pedido["id"]);
                Pedido::ActualizarEstadoPedido("listo para servir",$pedido["id"]);

                $log->registarActividad("{$nombre} esta preparando el pedido");
                $log->registarActividad("{$nombre} ya termino el pedido");

                Pedido::actualizarFechaEntrega($pedido["id"]);
            }
            Empleado::actualizarEstadoEmpleado(false, $idEmpleado);
            return "{$nombre} ya termino los pedidos pendientes";
        } else{
            throw new Exception("no hay pedidos pendientes");
        }
    }
    public static function atenderCliente($pedido, $mesa, $numPedido){
        // Consigo el puesto según la comida, ej: "fernet con coca -> bartender"
        $encargado = self::obtenerEncargado($pedido);                
        $log = new registrador();
        // Obtengo todos los empleados que pueden ejecutar el pedido
        $empleadosEncontrados = self::obtenerEmpleadosPorPuesto($encargado);    

        if (count($empleadosEncontrados) > 0) {
            $i = array_rand($empleadosEncontrados);
            $dataProducto = Producto::buscarProducto($pedido);
            $mozos = self::obtenerEmpleadosPorPuesto("mozo");
            if (count($mozos) > 0){
                $i = array_rand($mozos);
                $idMozo = $mozos[$i]["id"];
                $nombreMozo = $mozos[$i]["nombre"];

                // bartender, cerbecero o cocinero
                $idEncargado = $empleadosEncontrados[$i]["id"];
                $nombreEncargado = $empleadosEncontrados[$i]["nombre"];
                    
                $log->registarActividad("{$nombreMozo} atiende a los clientes");
                $log->registarActividad("{$nombreMozo} le asigno la mesa {$mesa}");
                $log->registarActividad("{$nombreMozo} le asigno el pedido a {$nombreEncargado}");
                    
                $encargo = new Pedido();
                $idPedido = $encargo->guardar();
                Pedido::actualizarPedido($idPedido, $numPedido, $dataProducto["id"], $mesa, $idMozo, $idEncargado, false);
                $log->registarActividad("{$nombreMozo} dio de alta el pedido de la mesa {$mesa}");
                return $nombreEncargado;
            } else {
                throw new Exception("no hay mozos disponible");       
            }
        } else {
            throw new Exception("No hay empleados que puedan realizar el pedido");
        }
    }
    public static function calificarEmpleado($idEmpleado, $calificacion, $tipo) {
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT puntuacion FROM empleados WHERE id = :id AND tipo = :tipo");
        $select->bindParam(':id', $idEmpleado, PDO::PARAM_INT);
        $select->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $select->execute();
        $result = $select->fetch(PDO::FETCH_ASSOC);
        $puntuaciones = [];
        if(!empty($result)){
            foreach($result as $punto){
                $puntuaciones[] = intval($punto);
            }
        }
        $puntuaciones[] = $calificacion;
        $promedio = array_sum($puntuaciones) / count($puntuaciones);
        $update = $bd->prepararConsulta("UPDATE empleados SET puntuacion = :puntuacion WHERE id = :id AND tipo = :tipo");
        $update->bindParam(':puntuacion', $promedio, PDO::PARAM_STR); 
        $update->bindParam(':id', $idEmpleado, PDO::PARAM_INT);
        $update->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $update->execute();
    }
    public function guardar() { 
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO empleados (nombre, tipo, ocupado)
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
    public static function rotarPersonal($id, $nuevoPuesto){
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "UPDATE empleados SET tipo = :tipo WHERE id = :id";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':tipo', $nuevoPuesto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
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
    public static function ObtenerEmpleado($id){
        try {
            $bd = AccesoDatos::obtenerInstancia();
            $query = "SELECT * FROM empleados WHERE id = :id AND deleted_at IS NULL";
            $select = $bd->prepararConsulta($query);
            $select->bindParam(':id', $id, PDO::PARAM_STR);
            $select->execute();
            return $select->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
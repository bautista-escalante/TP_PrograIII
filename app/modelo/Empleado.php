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
class Empleado
{
    public $id;
    public $nombre;
    public $tipo;
    public $clave;
    public $ocupado;
    public function __construct($nombre, $tipo, $clave)
    {
        $this->id = null;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->ocupado = false;
        $this->clave = $clave;
    }
    public static function obtenerPedidos($id)
    {
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT * FROM pedidos WHERE  AND estado = 'en preparacion'");
        $select->bindValue(":id", $id, PDO::PARAM_INT);
        $select->execute();
        $pedidos = $select->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($pedidos)) {
            return $pedidos;
        } else {
            throw new Exception("no hay pedidos que preparar");
        }
    }
    public static function atenderPedido($idEmpleado, $pedido)
    {
        $log = new registrador();

        $empleado = Empleado::obtenerEmpleadoId($idEmpleado);
        $nombre = $empleado["nombre"];

        Empleado::actualizarEstadoEmpleado(true, $idEmpleado);

        Pedido::ActualizarEstadoPedido("listo para servir", $pedido);

        $log->registarActividad("{$nombre} esta preparando el pedido");
        $log->registarActividad("{$nombre} ya termino el pedido");

        Empleado::actualizarEstadoEmpleado(false, $idEmpleado);
        return "{$nombre} ya termino el pedido pendiente";
    }
    public static function atenderCliente($pedido, $mesa, $numPedido, $idMozo)
    {
        $dataProducto = Producto::buscarProducto($pedido);
        
        $mozo = Empleado::ObtenerEmpleadoId($idMozo);
        $nombreMozo = $mozo["nombre"];
        
        $log = new registrador();
        $log->registarActividad("{$nombreMozo} atiende a los clientes");
        $log->registarActividad("{$nombreMozo} le asigno la mesa {$mesa}");

        $encargo = new Pedido();
        $idPedido = $encargo->guardar();
        Pedido::actualizarPedido($idPedido, $numPedido, $dataProducto["id"], $mesa, $idMozo, false);
        $log->registarActividad("{$nombreMozo} dio de alta el pedido de la mesa {$mesa}");
    }
    public static function calificarEmpleado($idEmpleado, $calificacion, $tipo)
    {
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT puntuacion FROM empleados WHERE id = :id AND tipo = :tipo");
        $select->bindParam(':id', $idEmpleado, PDO::PARAM_INT);
        $select->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $select->execute();
        $result = $select->fetch(PDO::FETCH_ASSOC);
        $puntuaciones = [];
        if (!empty($result)) {
            foreach ($result as $punto) {
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
    public function guardar()
    {
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        try {
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("INSERT INTO empleados (nombre, tipo, clave, ocupado) VALUES (:nombre, :tipo, :clave, :ocupado)");
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_INT);
            $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
            $consulta->bindValue(':ocupado', $this->ocupado, PDO::PARAM_BOOL);
            $consulta->execute();
            $this->id = $bd->obtenerUltimoId();
            return "usuario creado con exito";
        } catch (PDOException) {
            return "error al crear el usuario";
        }
    }

    public static function actualizarEstadoEmpleado($nuevoEstado, $id)
    {
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE empleados SET ocupado = :ocupado WHERE id = :id");
        $consulta->bindValue(':ocupado', $nuevoEstado, PDO::PARAM_BOOL);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function rotarPersonal($id, $nuevoPuesto)
    {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "UPDATE empleados SET tipo = :tipo WHERE id = :id";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':tipo', $nuevoPuesto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function obtenerEmpleadosPorPuesto($puesto)
    {
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
    public static function ObtenerEmpleado($nombre)
    {
        try {
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT * FROM empleados WHERE nombre = :nombre AND deleted_at IS NULL");
            $select->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $select->execute();
            return $select->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public static function ObtenerEmpleadoId($id)
    {
        try {
            $bd = AccesoDatos::obtenerInstancia();
            $select = $bd->prepararConsulta("SELECT * FROM empleados WHERE id = :id AND deleted_at IS NULL");
            $select->bindParam(':id', $id, PDO::PARAM_STR);
            $select->execute();
            return $select->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

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
class Empleado{
    public $id;
    public $nombre;
    public $pendientes;
    public $tipo;
    public $ocupado;
    public function __construct($nombre, $tipo)
    {
        $this->id = null;
        $this->nombre = $nombre;
        $this->pendientes = [];
        $this->tipo = $tipo;
        $this->ocupado = false;
    } 
    public function agregarPedido($pedido){
        $this->pendientes[]=$pedido;
    } 
    public function atenderPedidos($idPedido){
        // en empleadocontroler debo dividir segun el tipo
        if($this->tipo != "mozo"){
            $this->actualizarEstadoEmpleado(true);
            $elemento = array_rand($this->pendientes);
            $pedido = $this->pendientes[$elemento];
            echo "el pedido de ".$pedido->nombrePedido."esta a cargo de ". $this->nombre ."<br>";
            $this->guardarOperacion($pedido);
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'en preparacion' AND idPedido = :idpedido");
            $consulta->bindValue(":id",$idPedido,PDO::PARAM_STR);
            $consulta->execute();
            $pedido = $consulta->fetchObject("Pedido");
            if(time() - $pedido->timestamp >= $pedido->tiempo){
                $pedido->actualizarEstadoPedido("listo para servir");
                $this->actualizarEstadoEmpleado(false);
                echo "tu pedido esta listo<br>";
            }
            echo "tu pedido todabia esta en preparacion";
        }
        else{
            echo "error. al mozo no le corresponde esta tarea<br>";
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
    private function guardarOperacion($pedido,){
        $nuevaOperacion=array(
            "nombreEmpleado"=> $this->nombre,
            "puesto" => $this->tipo,
            "nombrePedido"=> $pedido->nombrePedido,
            "idPedido"=> $pedido->idPedido,
            "tiempo"=> $pedido->tiempo,
            "cantidad"=> $pedido->cantidad,
            "mesa"=>null,
            "nombreMozo"=> null); 
        $archivo = file_get_contents("Operaciones.json");
        $operacionesAnteriores = json_decode($archivo,true);
        if(!empty($operacionesAnteriores)){
            $operacionesAnteriores [] = $nuevaOperacion;
        } 
        else {
            $operacionesAnteriores = $nuevaOperacion;
        }
        if(file_put_contents("Operaciones.json",json_encode($operacionesAnteriores,true,JSON_PRETTY_PRINT))){
            echo " el arcvhivo Operaciones fue escrito";
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
    // este metodo debe llamarse primero
    public function atenderCliente($pedido,$nombre,$foto=null){
        if($this->tipo == "mozo"){
            // consigo el puesto para el segun la comida
            $encargado = Empleado::obtenerEncargado($pedido);
            if($encargado != false){

                echo("levantar el pedido<br>");
                $encargo = new Pedido("en preparacion",$nombre, $pedido,1);
                //$encargo->guardar();
                
                echo("asignar mesa al cliente<br>");
                $mesa = new Mesa();

                echo("entrego el pedido a los empleados correspondientes ");
                //encuentro todos los empleados de ese rubro y eligo uno de manea aleatoria
                $empleadosEncontrados = Empleado::obtenerEmpleadosPorPuesto($encargado);
                $empleadoEncontrado = array_rand($empleadosEncontrados);
                
                $empleado = new Empleado($empleadoEncontrado["nombre"],$empleadoEncontrado["tipo"]);
                $empleado->agregarPedido($encargo);
                //$empleado->atenderPedidos($encargo->idPedido);

                //$cliente = new Cliente($nombre,$encargo->idPedido,$foto,$mesa->idMesa, $empleadoEncontrado["id"],$this->id);
                //$cliente->actualizarOperacion($mesa->idMesa, $this->nombre);
            }
        }
        else{
            echo "esta tarea debe ser realizada unicamente por el mozo";
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
    public static function obtenerEncargado($comida){
        $archivo = file_get_contents("modelo/menu.json");
        $menu = json_decode($archivo,true);
        $encontrado = false;
        foreach($menu as $pedido){
            if($pedido["nombre"] == $comida){
                $encontrado = true;
                return $pedido["encargado"];
            }
        }
        if($encontrado == false){
            echo("error no tenemos esa comida en el menu<br>");
            return false;
        }
    }
}
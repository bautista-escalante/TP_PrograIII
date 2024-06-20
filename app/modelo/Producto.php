<?php
/* 

metodos -> calcularTiempo (genera un numeo ramdom de 1 a 60) para calcular segundos
        -> guardar
        -> actualizar estado();
*/
include_once "db/AccesoDatos.php";
class Producto {
    public $id;
    public $nombre;
    public $puestoResponsable;
    public $precio;
    public $fechaBaja;
    public function __construct($nombre, $puestoResponsable,$precio) {
        $this->id =null;
        $this->nombre = $nombre;
        $this->puestoResponsable = $puestoResponsable;
        $this->precio = $precio;
        $this->fechaBaja = null;
    }
    public function guardar() {
        try{
            $bd = AccesoDatos::obtenerInstancia();
            $sql = "INSERT INTO producto (nombre, puestoResponsable, precio, fechaBaja)
            VALUES (:nombre, :puestoResponsable, :precio, :fechaBaja)";
            $consulta = $bd->prepararConsulta($sql);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':puestoResponsable', $this->puestoResponsable, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
            $consulta->execute();
            $this->id = $bd->obtenerUltimoId();
        }catch(PDOException){
            echo "error guardando el producto";
        }
    }
    public static function eliminarProducto($id){
        try{
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("UPDATE producto SET fechaBaja = :fechaBaja WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':fechaBaja', (new DateTime())->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->execute();
        }catch(PDOException){
            echo "error eliminando el producto<br>";
        }
    }
    public static function modificarPrecioProducto($id,$nuevoPrecio){
        try{
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("UPDATE producto SET precio = :precio WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $nuevoPrecio, PDO::PARAM_STR);
            $consulta->execute();
            echo "datos actializados correctamente <br>";
        }catch(PDOException){
            echo "error actualizando los datos <br>";
        }
    }
    public static function mostrarProductos(){
        $db = AccesoDatos::obtenerInstancia();
        $select = $db->prepararConsulta("SELECT * FROM producto WHERE fechaBaja IS NULL");
        $select->execute();
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function obtenerProducto($comida){
        $producto = Producto::buscarProducto($comida);
        if($producto!=false){
            return $producto;
        }
        else{
            echo "error no tenemos esa comida en nuestro menu";
        }
    }
    public static function buscarProducto($productoNombre) {
        $productos = Producto::mostrarProductos();
        foreach($productos as $producto){
            if($producto["nombre"] == $productoNombre){
                return $producto;
            }
        }
        return false;
    }
    public static function generarEstadisticaProductos(){
        // la probabilidad de que se venda 	Caipirinha  es de ... %
        $bd = AccesoDatos::obtenerInstancia();
        $select = $bd->prepararConsulta("SELECT idProducto FROM pedidos");
        $select->execute();
        $productos = $select->fetchAll(PDO::FETCH_ASSOC);

        $p = 0;
        foreach($productos as $producto){
            if(intval($producto["idProducto"]) === 7){
                $p++;
            }
        }
        return ($p / count($productos));
    }
}
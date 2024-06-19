<?php
include_once "db/AccesoDatos.php";
class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $puesto;

    public function __construct($usuario,$puesto,$clave)
    {
        $this->usuario = $usuario;
        $this->puesto = $puesto;
        $this->clave = $clave;
    }
    public function crearUsuario()
    {
        // retorna el id 
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, puesto) VALUES (:usuario, :clave, :puesto)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':puesto', $this->puesto);
        $consulta->execute();
        return $this->id =  $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario AND fechaBaja IS NULL");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
    public static function modificarUsuario($id, $clave, $usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}
<?php
/*
    id(de 5 caracteres) - estado - puntuacion
    debe tener acceso a los tiempos  restanters para eso debo crear algun tipo de timer
    puntuacion
*/
class Mesa{
    public $id;
    public $idMesa;
    public $estado;
    public $puntuacion;

    public function __construct(){
        $this->id = null;
        $this->puntuacion = null;
        $this->estado = "cerrada";
        $this->idMesa = $this->generarIdMesa();
    }
    private function generarIdMesa() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }
    public static function AsignarMesa(){
        try{
            $db = AccesoDatos::obtenerInstancia();
            $consulta=$db->prepararConsulta("SELECT * FROM mesas  WHERE estado = 'cerrada'");
            $consulta->execute();
            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($resultado)) {
                $mesa = $resultado[rand(0,count($resultado)-1)];
                
                return $mesa;
            } else {
                echo "no hay mesas disponibles";
                return null;
            }
        } catch (PDOException) {
            error_log("Error al asignar mesa" );
            return null;
        }
    }
    public function guardar() {
        $bd = AccesoDatos::obtenerInstancia();
        $sql = "INSERT INTO mesas (estado, puntuacion, idMesa) VALUES (:estado, :puntuacion, :idMesa)";
        $consulta = $bd->prepararConsulta($sql);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacion', $this->puntuacion, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->execute();
        $this->id = $bd->obtenerUltimoId();
    }
    public static function ActualizarEstadoMesa($nuevoEstado){
        self::$estado = $nuevoEstado;
        $bd = AccesoDatos::obtenerInstancia();
        $consulta = $bd->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', self::$estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', self::$id, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function CalificarMesa($idmesa, $calificacion){
        if($idmesa == self::$idMesa && self::$estado = "cerrada"){
            $bd = AccesoDatos::obtenerInstancia();
            $consulta = $bd->prepararConsulta("SELECT puntuacion FROM mesas WHERE idmesa = :idmesa");
            $consulta->bindParam(':idmesa', $idmesa, PDO::PARAM_STR);
            $consulta->execute();
            $result = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['puntuacion'])) {
                $puntuaciones = json_decode($result['puntuacion'], true);
                $puntuaciones[] = $calificacion;
                $promedio = array_sum($puntuaciones) / count($puntuaciones);
            } else {
                $promedio = $calificacion;    
            }
            $bd = AccesoDatos::obtenerInstancia();
            $update = $bd->prepararConsulta("UPDATE mesas SET puntuacion = :puntuacion WHERE idmesa = :idmesa");
            $update->bindParam(':puntuacion', $promedio, PDO::PARAM_STR);
            $update->bindParam(':idmesa', $idmesa, PDO::PARAM_INT);
            $update->execute();
        }
    }
    
}
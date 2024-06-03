<?php
include_once "modelo/Usuario.php";
include_once "db/AccesoDatos.php";
function registrarIngreso($nombre, $clave){
    $usuario = Usuario::obtenerUsuario($nombre);
    $nuevoRegistro = array(
        "id" => $usuario->id,
        "nombre"=>$nombre,
        "fecha"=> date("y-m-d h:m:s")
    );
    $archivo = file_get_contents("modelo/Registro.json");
    $registros = json_decode($archivo,true);
    if($usuario->usuario == $nombre && $usuario->clave == $clave){
        if(!empty($registros)){
            $registros [] = $nuevoRegistro;
        }else{
            $registros = $nuevoRegistro;
        }
        return file_put_contents("modelo/Registro.json",$jsonRegistros = json_encode($registros, JSON_PRETTY_PRINT),JSON_PRETTY_PRINT);
    }else{
        echo "el usuario no existe";
        return false;
    }
}



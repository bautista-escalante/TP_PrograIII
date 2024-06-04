<?php
/* 
los socios pueden ver
7- De los empleados:
    a- Los días y horarios que se ingresaron al sistema.
    b- Cantidad de operaciones de todos por sector.
    c- Cantidad de operaciones de todos por sector, listada por cada empleado.
    d- Cantidad de operaciones de cada uno por separado.
    e- Posibilidad de dar de alta a nuevos, suspenderlos o borrarlos.
8- De las pedidos:
    a- Lo que más se vendió.
    b- Lo que menos se vendió.
    c- Los que no se entregaron en el tiempo estipulado.
    d- Los cancelados.
9- De las mesas:
    a- La más usada.
    b- La menos usada.
    c- La que más facturó.
    d- La que menos facturó.
    e- La/s que tuvo la factura con el mayor importe.
    f- La/s que tuvo la factura con el menor importe.
    g- Lo que facturó entre dos fechas dadas.
    h- Mejores comentarios.
    i- Peores comentarios.
*/
include_once "modelo/Socio.php";
include_once "modelo/Empleado.php";

function suspender($id){
    $db = AccesoDatos::obtenerInstancia();
    $select = $db->prepararConsulta("SELECT * FROM empleados WHERE id= :id");
    $select->bindValue(":id",$id,PDO::PARAM_INT);
    $select->execute();
    $empleado = $select->fetch(PDO::FETCH_ASSOC);
    if($empleado !=null){
        Socio::suspenderEmpleado($empleado);
    } else{
        echo "error";
    }
}

<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Mesa.php";

class MesaControler {
    public function agregarMesa(Request $request, Response $response, $args) {
        $mesa = new Mesa();
        $mesa->guardar();
        $response->getBody()->write("Mesa agregada correctamente.<br>");
        return $response->withStatus(201);
    }
    public function borrarMesa(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $id = $params['id'];
        if (!empty($id)) {
            Mesa::borrarMesa($id);
            $response->getBody()->write("Mesa borrada correctamente.<br>");
            return $response->withStatus(200); 
        } else {
            $response->getBody()->write("Error: coloca los parámetros para borrar la mesa.<br>");
            return $response->withStatus(400);
        }
    }
    public function modificarMesa(Request $request, Response $response, $args) {
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params['id'];
            $puntos = $params['puntos'];
            if (isset($id) && !empty($id) && isset($puntos) && !empty($puntos)) {
                Mesa::modificarMesa($id, $puntos);
                $response->getBody()->write("Mesa modificada.<br>");
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("Error: coloca los parámetros para modificar la mesa.<br>");
                return $response->withStatus(400);
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }
    public function listarMesas(Request $request, Response $response, $args){
        $mesas = Mesa::MostarMesas();
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['mesa', 'estado']);
        foreach ($mesas as $mesa){
            fputcsv($csv, [$mesa["id"], $mesa["estado"]]);
        }
        rewind($csv);
        $response->getBody()->write(stream_get_contents($csv));
        fclose($csv);
        return $response->withHeader('Content-Type', 'text/csv')
                        ->withHeader('Content-Disposition', 'attachment; filename="mesas.csv"');
    }
}

<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\ExpiredException;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include_once "modelo/Empleado.php";
include_once "modelo/Cliente.php";
include_once "modelo/Pedido.php";
include_once "modelo/Mesa.php";

class ComandaController
{
    public function atenderCliente(Request $request, Response $response)
    {
        $param = $request->getParsedBody();
        try {
            if (!empty($param["codigoPedido"]) && !empty($param["tiempo"]) &&
                isset($param["codigoPedido"], $param["tiempo"])) {

                // el empleado le asiga el tiempo
                Pedido::asignarTiempo($param["codigoPedido"], $param["tiempo"]);

                Pedido::actualizarEncargado($param["codigoPedido"], $this->obtenerId($request));

                $estado = Empleado::atenderPedido($this->obtenerId($request), $param["codigoPedido"]);
                
                $response->getBody()->write(json_encode(["ESTADO" => $estado]));
            } else {
                $producto = Pedido::listarPedidosPendientes($this->obtenerId($request));
                $response->getBody()->write(json_encode(["PENDIENTES" => $producto]));
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function obtenerId(Request $request)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $jwt = JWT::decode($token, new Key($_ENV["secretKey"], 'HS256'));
            $data = (array) $jwt;
            if (isset($data['idEmpleado'])) {
                return $data['idEmpleado'];
            }
        } catch (ExpiredException) {
            throw new Exception("tu sesion ya caduco, por favor vuelve a ingresar.");
        } catch (Exception $e) {
            throw new Exception('Excepción al decodificar el token JWT: ' . $e->getMessage());
        }
    }

    public function cerrarMesa(Request $request, Response $response, $args)
    {
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params['id'];

            if (isset($id) && !empty($id)) {

                $mesa = Mesa::MostarMesa($id);
                if (!$mesa) {
                    throw new Exception("Mesa no encontrada.");
                }
                if ($mesa['estado'] === "con cliente pagando") {

                    Mesa::ActualizarEstadoMesa($id, "cerrada");

                    $response->getBody()->write(json_encode(["ESTADO"=>"mesa $id cerrada"]));
                } else {
                    throw new Exception("Error: para cerrar la mesa el cliente debe pagarla la cuenta primero");
                }
            } else {
                $mesas = Mesa::MostarMesas();
                $mesasPendientes = [];

                foreach($mesas as $mesa){
                    if($mesa["estado"] == "con cliente pagando"){
                        $mesasPendientes[] = $mesa["id"];  
                    }
                }

                if(!empty($mesasPendientes)){
                    $response->getBody()->write(json_encode(["PENDIENTES" => $mesasPendientes]));
                } else {
                    throw new Exception("no hay mesas a las que cobrar");
                }
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cancelarPedido(Request $request, Response $response, $args)
    {
        try {
            parse_str(file_get_contents('php://input'), $params);
            $alfa = $params['CodigoAlfa'];
            if (isset($alfa) && !empty($alfa)) {
                $estado = Pedido::cancelarPedido($alfa);
                $response->getBody()->write(json_encode(["ESTADO" => $estado]));
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "faltan parametros"]));
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cobrar(Request $request, Response $response, $args)
    {
        try {
            parse_str(file_get_contents('php://input'), $params);
    
            if (isset($params["id"]) && !empty($params["id"])) {
                $id = $params["id"];
    
                $mesa = Mesa::MostarMesa($id);
    
                if (!$mesa) {
                    throw new Exception("Mesa no encontrada.");
                }
    
                if ($mesa['estado'] === "el cliente esta comiendo") {
                    
                    $precio = Pedido::obtenerPrecio($id);
                    if ($precio == 0) {
                        throw new Exception("No hay pedidos para cobrar en esta mesa.");
                    }
                    
                    $log = new registrador();
                    $log->registarActividad("el mozo cobra la suma de $ {$precio} de la mesa {$id}");

                    Mesa::ActualizarEstadoMesa($id, "con cliente pagando");
    
                    $response->getBody()->write(json_encode(["INGRESO" => $precio]));
                } else {
                    throw new Exception("Error: para cobrar, el cliente debe estar comiendo.");
                }
            } else {
                $mesas = Mesa::MostarMesas();
                $mesasPendientes = [];

                foreach($mesas as $mesa){
                    if($mesa["estado"] == "el cliente esta comiendo"){
                        $mesasPendientes[] = $mesa["id"];  
                    }
                }

                if(!empty($mesasPendientes)){
                    $response->getBody()->write(json_encode(["PENDIENTES" => $mesasPendientes]));
                } else {
                    throw new Exception("no hay mesas a las que cobrar");
                }
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function puntuar(Request $request, Response $response)
    {
        parse_str(file_get_contents('php://input'), $params);
        try {
            if (isset($params["codigoAlfa"], $params["calificacionMozo"], $params["comentarioMozo"],
                $params["calificacionCocinero"], $params["comentarioCocinero"], $params["calificacionMesa"],
                $params["comentarioMesa"], $params["calificacionRestaurante"], $params["comentarioRestaurante"]) 
                && !empty($params["codigoAlfa"]) &&
                !empty($params["calificacionMozo"]) && !empty($params["comentarioMozo"]) &&
                !empty($params["calificacionCocinero"]) && !empty($params["comentarioCocinero"]) &&
                !empty($params["calificacionMesa"]) && !empty($params["comentarioMesa"]) &&
                !empty($params["calificacionRestaurante"]) && !empty($params["comentarioRestaurante"])) {

                $pedido = Pedido::obtenerPedido($params["codigoAlfa"]);
                if (!empty($pedido)) {

                    Cliente::calificar($pedido["idMesa"], $pedido["idCocinero"], $pedido["idMozo"],
                        $params["codigoAlfa"], $params["calificacionMesa"], $params["comentarioMesa"],
                        $params["calificacionCocinero"], $params["comentarioCocinero"], $params["calificacionMozo"],
                        $params["comentarioMozo"], $params["calificacionRestaurante"], $params["comentarioRestaurante"]);

                    $response->getBody()->write(json_encode(["ESTADO" => "los comentarios fueron guardados"]));
                } else {
                    throw new Exception("no se encontro el pedido");
                }
            } else {
                throw new Exception("faltan parametros");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verEstadisticas(Request $request, Response $response)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        try {
            $lunes = Pedido::estadisticasMensuales("Monday");
            $martes = Pedido::estadisticasMensuales("Tuesday");
            $miercoles = Pedido::estadisticasMensuales("Wednesday");
            $jueves = Pedido::estadisticasMensuales("Thursday");
            $viernes = Pedido::estadisticasMensuales("Friday");
            $sabado = Pedido::estadisticasMensuales("Saturday");
            $domingo = Pedido::estadisticasMensuales("Sunday");

            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo lunes es de " . $lunes, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo martes es de " . $martes, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo miercoles es de " . $miercoles, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo jueves es de " . $jueves, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo viernes es de " . $viernes, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo sabado es de " . $sabado, 0, 1);
            $pdf->Cell(0, 10, "La probabilidad de que se venda mas siendo domingo es de " . $domingo, 0, 1);

        } catch (Exception $e) {
            $pdf->Cell(0, 10, $e->getMessage());
        }
        $pdfOutput = $pdf->Output('S');
        $response->getBody()->write($pdfOutput);
        return $response->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="estadisticas.pdf"');
    }
    public function verTiempoDemora(Request $request, Response $response)
    {
        $param = $request->getParsedBody();
        if (!empty($param["codigoPedido"]) && !empty($param["mesa"]) && isset($param["codigoPedido"], $param["mesa"])) {
            $tiempo = Pedido::tiempoDemora($param["codigoPedido"], $param["mesa"]);
            $response->getBody()->write(json_encode(["ESTADO" => $tiempo]));
        } else {
            $response->getBody()->write(json_encode(["ERROR" => "faltan parametros"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verMesaMasUsada(Request $request, Response $response)
    {
        try {
            $mesa = Pedido::verMesaMasUsada();
            $response->getBody()->write(json_encode(["MESA" => $mesa]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}

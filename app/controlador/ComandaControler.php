<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\ExpiredException;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;
include_once "modelo/Empleado.php";
include_once "modelo/Pedido.php";
include_once "modelo/Mesa.php";

class ComandaControler{
    public function atenderCliente(Request $request, Response $response){
        try{
            $estado = Empleado::atenderPedidos($this->obtenerId($request));
            $response->getBody()->write(json_encode(["ESTADO"=>$estado]));
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function obtenerId(Request $request){
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $jwt = JWT::decode($token, new Key($_ENV["secretKey"], 'HS256'));
            $data = (array) $jwt;
            if (isset($data['idEmpleado'])){
                return $data['idEmpleado'];
            }
            else{
                echo "error";
            }
        }catch (ExpiredException) {
            throw new Exception("tu sesion ya caduco, por favor vuelve a ingresar.");
        } catch (Exception $e) {
            throw new Exception('Excepción al decodificar el token JWT: ' . $e->getMessage());
        }
    }
    
    public function cerrarMesa(Request $request, Response $response, $args){
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params['id'];
            if (isset($id) && !empty($id)) {
                $mesa = Mesa::MostarMesa($id);
                if($mesa['estado'] === "con cliente pagando"){
                    Mesa::ActualizarEstadoMesa($id, "cerrada");
                    $response->getBody()->write("Mesa cerrada.<br>");
                    return $response->withStatus(200);
                }else{
                    $response->getBody()->write("Error: para cerrar la mesa el cliente debe pagarla la cuenta primero.<br>");
                    return $response->withStatus(400);
                }
            } else {
                $response->getBody()->write("Error: coloca los parámetros para cerrar la mesa.<br>");
                return $response->withStatus(400);
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }

    public function cancelarPedido(Request $request, Response $response, $args){
        try {
            parse_str(file_get_contents('php://input'), $params);
            $alfa = $params['CodigoAlfa'];
            if (isset($alfa) && !empty($alfa)) {
                $estado = Pedido::cancelarPedido($alfa);
                $response->getBody()->write(json_encode(["ESTADO"=>$estado]));
            }else{
                $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
        }
return $response->withHeader('Content-Type', 'application/json');
    }

    public function cobrar(Request $request, Response $response, $args){
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params["id"];
            if (isset($id) && !empty($id)){
                $mesa = Mesa::MostarMesa($id);
                if($mesa['estado'] === "el cliente esta comiendo"){
                    $precio = Pedido::obtenerPrecio($id);
                    $log = new registrador();
                    $log->registarActividad("el mozo cobra la suma de $ {$precio} de la mesa {$id}");
                    Mesa::ActualizarEstadoMesa($id, "con cliente pagando");
                    $response->getBody()->write("cliente pagando<br> el cliente pago $".$precio.".<br>");
                    return $response->withStatus(200);
                }else{
                    $response->getBody()->write("Error: para cobrar el pedido debio ser entregado.<br>");
                    return $response->withStatus(400);
                }
            } else {
                $response->getBody()->write("Error: coloca los parámetros para cobrar el pedido.<br>");
                return $response->withStatus(400);
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }

    public function puntuar(Request $request, Response $response){
        try {
            parse_str(file_get_contents('php://input'), $params);
            $idMesa = $params['idMesa'];
            $codigoAlfa = $params["codigoAlfa"];

            $pedidos = Pedido::obtenerPedido($codigoAlfa);
            foreach($pedidos as $pedido){
                if (isset($idMesa, $pedido["idCocinero"], $pedido["idMozo"]) && !empty($idMesa)&& !empty($pedido["idMozo"])&& !empty($pedido["idCocinero"])) {

                    $calificacionMozo = intval($params['calificacionMozo']);
                    $calificacionMesa = intval($params['calificacionMesa']);
                    $calificacionCocinero = intval($params['calificacionCocinero']);
                    $comentarioMozo = $params['comentarioMozo'];
                    $comentarioMesa = $params['comentarioMesa'];
                    $comentarioCocinero = $params['comentarioCocinero'];
                    $idCocinero = $pedido["idCocinero"];
                    $idMozo  = $pedido["idMozo"];

                    $mesa = Mesa::MostarMesa($idMesa);
                    if(!empty($mesa) && $mesa['estado'] === "cerrada"){
                        Pedido::guardarPuntuacion($calificacionMozo, $comentarioMozo, $calificacionCocinero, $comentarioCocinero, $calificacionMesa, $comentarioMesa);
                        Mesa::CalificarMesa($idMesa,$calificacionMesa);
                        Empleado::calificarEmpleado($idMozo, $calificacionMozo, "mozo");
                        Empleado::calificarEmpleado($idCocinero, $calificacionCocinero, "cocinero");
                        $response->getBody()->write("su calificacion fue enviada.<br>");
                        return $response->withStatus(200);
                    }else{
                        $response->getBody()->write("Error: para puntuar el pedido debe estar pagado.<br>");
                        return $response->withStatus(400);
                    }
                } else {
                    $response->getBody()->write("Error: coloca los parámetros para puntuar.<br>");
                    return $response->withStatus(400);
                }
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }catch(ParseError) {
            $response->getBody()->write("Error  las calificaciones deben ser de tipo numerico <br>");
            return $response->withStatus(500);
        }
    }

    public function verEstadisticas(Request $request, Response $response){
        //generar estadisticas para mesas, proucto mas vendidos
        $params = $request->getQueryParams();
        $probabilidad = Producto::generarEstadisticaProductos($params["producto"]);
        $probabilidadTiempo = Pedido::GenerarEstadisticasPedido();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'La probabilidad de que el pedido se entrege a tiempo es de ' . number_format($probabilidadTiempo * 100, 2) . '%', 0, 1);
        if($probabilidad != -1){
            $pdf->Cell(0, 10, 'La probabilidad de que se venda '.$params["producto"].' es de ' . number_format($probabilidad * 100, 2) . '%', 0, 1);
        }
        else{
            $pdf->Cell(0, 10, "Error: el producto no esta dentro del menu");
        }
        $pdf->Output('F', 'php://output');
        $response = $response->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'attachment; filename="estadisticas.pdf"');
        return $response;
    }

    public function verTiempoDemora(Request $request, Response $response){
        $param = $request->getParsedBody();
        if(!empty($param["codigoPedido"]) && !empty($param["mesa"]) && isset($param["codigoPedido"], $param["mesa"])){
            $tiempo = Pedido::tiempoDemora($param["codigoPedido"], $param["mesa"]);
            $response->getBody()->write(json_encode(["ESTADO"=>$tiempo]));
        }else{
            $response->getBody()->write(json_encode(["ERROR"=>"faltan parametros"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verMesaMasUsada(Request $request, Response $response){
        try{
            $mesa = Pedido::verMesaMasUsada();
            $response->getBody()->write(json_encode(["MESA"=>$mesa["idMesa"]]));
        }catch(Exception $e){
            $response->getBody()->write(json_encode(["ERROR"=>$e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
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
    public function cocinar(Request $request, Response $response){
        Empleado::atenderPedidos($this->obtenerId($request));
        return $response;
    }
    public function prepararTrago(Request $request, Response $response){
        Empleado::atenderPedidos($this->obtenerId($request));
        return $response;
    }
    public function servirCerveza(Request $request, Response $response){
        Empleado::atenderPedidos($this->obtenerId($request));
        return $response;
    }
    private function obtenerId(Request $request){
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $jwt = JWT::decode($token, new Key($_ENV["secretKey"], 'HS256'));
            $data = (array) $jwt;
            if (isset($data['idEmpleado'])){
                echo $data['idEmpleado'];
                return $data['idEmpleado'];
            }
            else{
                echo "error <br>";
            }
        }catch (ExpiredException) {
            $response = new Response();
            $response->getBody()->write("tu sesion ya caduco, por favor vuelve a ingresar.");
            return $response;
        } catch (Exception $e) {
            error_log('Excepción al decodificar el token JWT: ' . $e->getMessage());
        }
    }
    public function cerrarMesa(Request $request, Response $response, $args){
        try {
            $id = $args['id'];
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
    public function cobrar(Request $request, Response $response, $args){
        try {
            $id = $args['id'];
            if (isset($id) && !empty($id)) {
                $mesa = Mesa::MostarMesa($id);
                if($mesa['estado'] === "el cliente esta comiendo"){
                    Mesa::ActualizarEstadoMesa($id, "con cliente pagando");
                    $response->getBody()->write("cliente pagando<br> el cliente pago $".Pedido::VerPrecio($id).".<br>");
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
            $params = $request->getParsedBody();
            $idMesa = $params['idMesa'];
            $pedidos = Pedido::obtenerPedido($idMesa);
            foreach($pedidos as $pedido){
                $calificacionMozo = $params['calificacionMozo'];
                $calificacionMesa = $params['calificacionMesa'];
                $calificacionCocinero = $params['calificacionCocinero'];
                $idCocinero = $pedido["idCocinero"];
                $idMozo  = $pedido["idMozo"];
                if (isset($idMesa, $idCocinero, $idMozo) && !empty($idMesa)&& !empty($idMozo)&& !empty($idCocinero)) {
                    $mesa = Mesa::MostarMesa($idMesa);
                    if($mesa['estado'] === "con cliente pagando"){
                        Mesa::CalificarMesa($idMesa,$calificacionMesa);
                        Empleado::calificarEmpleado($idMozo, $calificacionMozo, "mozo");
                        Empleado::calificarEmpleado($idCocinero, $calificacionCocinero, "cocinero");
                        $response->getBody()->write("su calificacion fue enviada.<br>");
                        return $response->withStatus(200);
                    }else{
                        $response->getBody()->write("Error: para puntuar el pedido ebe estar pagado.<br>");
                        return $response->withStatus(400);
                    }
                } else {
                    $response->getBody()->write("Error: coloca los parámetros para cobrar el pedido.<br>");
                    return $response->withStatus(400);
                }
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }
    public function verEstadisticas(Request $request, Response $response){
        //generar estadisticas para mesas, proucto mas vendidos
            $params = $request->getQueryParams();
            $name = $params['name'] ?? 'Invitado';
            
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, '¡Hola, ' . $name . '!');

            $pdf->Output('F', 'php://output');
        
            $response = $response->withHeader('Content-Type', 'application/pdf')
                                 ->withHeader('Content-Disposition', 'attachment; filename="estadisticas.pdf"');
            return $response;
    }
}
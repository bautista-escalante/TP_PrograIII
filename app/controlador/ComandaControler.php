<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\ExpiredException;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;
include_once "modelo/Empleado.php";
class ComandaControler{
    public function atender(Request $request, Response $response){
        $files = $request->getUploadedFiles();
        $param = $request->getParsedBody();
        if(isset($param["pedido"],$param["nombre"],$files['foto'])&& !empty($files["foto"])){
            if ($files['foto']->getError() === UPLOAD_ERR_OK) {
                $dataFoto = file_get_contents($files['foto']->getStream()->getMetadata('uri'));
            } else {
                $dataFoto = null;
            }
            Empleado::atenderCliente($param["pedido"],$param["nombre"], $dataFoto);
            $response->getBody()->write("Cliente atendido");
            return $response->withStatus(200);
        }
        return $response->withStatus(400);
    } 
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
                echo $data['idEmpleaod'];
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
}
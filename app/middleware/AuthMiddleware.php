<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\ExpiredException;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;
require_once '../vendor/autoload.php';
class AuthMiddleware
{
    private $sectorRequerido;
    public function __construct(string $sectorRequerido)
    {
        $this->sectorRequerido = $sectorRequerido;
    }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($this->verificarToken($request)) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "no tenes permiso, debe ser realizado por un ".$this->sectorRequerido));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    private function verificarToken(Request $request){
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $jwt = JWT::decode($token, new Key($_ENV["secretKey"], 'HS256'));
            $data = (array) $jwt;
            if (isset($data['sector'])){
                if ($this->sectorRequerido === $data['sector']){
                    return true;
                }
            }
        }catch (ExpiredException) {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "tu sesion ya caduco, por favor vuelve a ingresar."));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } catch (Exception $e) {
            error_log('Excepción al decodificar el token JWT: ' . $e->getMessage());
        }
        return false;
    }
}
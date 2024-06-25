<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
include_once "modelo/Registador.php";

class AuthMiddleware
{
    private $sectorRequerido;

    public function __construct(string $sectorRequerido)
    {
        $this->sectorRequerido = $sectorRequerido;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $verificacion = $this->verificarToken($request);
        
        if ($verificacion === true) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array("mensaje" => $verificacion));
            if ($this->sectorRequerido == "socio") {
                $log = new registrador();
                $log->registrarError("El empleado no tiene permiso para realizar esta acción");
            }
            $response->getBody()->write($payload);
            $response = $response->withHeader('Content-Type', 'application/json');
            if ($verificacion === "tu sesion ya caduco, por favor vuelve a ingresar.") {
                return $response->withStatus(401);
            }
        }
        return $response;
    }
    private function verificarToken(Request $request)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $jwt = JWT::decode($token, new Key($_ENV["secretKey"], 'HS256'));
            $data = (array) $jwt;
            if (isset($data['sector']) && $this->sectorRequerido === $data['sector']) {
                return true;
            }
        } catch (ExpiredException $e) {
            return "tu sesion ya caduco, por favor vuelve a ingresar.";
        }catch (SignatureInvalidException $e) {
            return "Firma del token no válida.";
        } catch (Exception $e) {
            return "Error en el token ".$e->getMessage();
        }
        return "no tenes permiso, debe ser realizado por un " . $this->sectorRequerido;
    }
}

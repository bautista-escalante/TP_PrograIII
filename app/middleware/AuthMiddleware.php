<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
class AuthMiddleware
{
    private $sectorRequerido;
    public function __construct(string $sectorRequerido)
    {
        $this->sectorRequerido = $sectorRequerido;
    }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($this->sectorRequerido === 'socio') {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "no tenesa permiso"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}


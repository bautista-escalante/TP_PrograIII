<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
class AuthMiddleware
{
    private $sectorRequerido;
    private $sectorRecibido;
    public function __construct(string $sectorRequerido, string $sectorRecibido)
    {
        $this->sectorRequerido = $sectorRequerido;
        $this->sectorRecibido = $sectorRecibido;
    }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($this->sectorRequerido === $this->sectorRecibido) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "no tenes permiso, debe ser realizado por un ".$this->sectorRequerido));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}


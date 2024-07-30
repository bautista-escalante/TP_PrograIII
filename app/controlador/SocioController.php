<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

include_once "modelo/Socio.php";
include_once "modelo/Empleado.php";
class SocioController
{
    public function ingresar(Request $request, Response $response)
    {
        $param = $request->getParsedBody();
        try {
            if (isset($param["nombre"], $param["clave"])) {
                
                $token = $this->registrarIngreso($param["nombre"], $param["clave"]);

                if ($token != false) {
                    $response->getBody()->write(json_encode(["JWT" => $token]));
                }
            } else {
                throw new Exception("error debes colocar el usuario y contraseña");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    private function registrarIngreso($nombre, $clave)
    {
        try{
            $usuario = Empleado::ObtenerEmpleado($nombre);

        }catch( Exception $e){
            throw $e;
        }
        if (empty($usuario)) {
            throw new Exception("usuario inexistente o despedido");

        } else if (!password_verify($clave, $usuario["clave"])) {
            throw new Exception("clave incorreta");
            
        }
        if (!empty($usuario)) {
            try {
                $puesto = $usuario["tipo"];
                if ($puesto != "socio") {

                    //registar accion
                    $log = new registrador();
                    $log->registarActividad("{$nombre} inicio sesion como {$puesto}");
                }
                $payload = [
                    'iat' => time(),
                    'exp' => time() + 9000, // 15 min
                    'sector' => $puesto,
                    'idEmpleado' => $usuario["id"]
                ];
                return JWT::encode($payload, $_ENV["secretKey"], 'HS256');
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            throw new Exception("error no tienes cuenta dentro del sistema");
        }
    }
    public function contratar(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (
                isset($parametros["nombre"], $parametros["puesto"], $parametros["clave"], $parametros["clave"])
                && !empty($parametros["nombre"]) && !empty($parametros["puesto"]) && !empty($parametros["clave"])) {

                if ($parametros["puesto"] == "socio") {
                    throw new Exception("no se puede agregar mas socios");
                }
                if(!empty(Empleado::ObtenerEmpleado($parametros["nombre"]))){
                    throw new Exception("ese nombre se usuario ya existe");
                }

                $mensaje = Socio::contratarEmpleado($parametros["nombre"], $parametros["puesto"], $parametros["clave"]);

                $response->getBody()->write(json_encode(["ESTADO" => $mensaje]));
            } else {
                throw new Exception("faltan parametros");
            }
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(["ERROR" => $ex->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function despedir(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $id = $params['id'];
        if (!empty($id)) {
            $mensaje = Socio::despedirEmpleado($id);
            $response->getBody()->write(json_encode(["ESTADO" => $mensaje]));
        } else {
            $response->getBody()->write(json_encode(["ERROR" => "faltan parametros"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function suspender(Request $request, Response $response, $args)
    {
        try {
            parse_str(file_get_contents('php://input'), $params);
            $id = $params['id'];
            if (isset($id) && !empty($id)) {
                Socio::suspenderEmpleado(intval($id));
                $response->getBody()->write("Empleado suspendido.<br>");
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "faltan parametros"]));
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function listarEmpleados(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $puesto = $params["puesto"];
        $empleados = Empleado::obtenerEmpleadosPorPuesto($puesto);
        
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Nombre', 'tipo', 'puntuacion']);
        foreach ($empleados as $empleado) {
            if($empleado["puntuacion"] == null){
                $empleado["puntuacion"] = 0;
            }
            fputcsv($csv, [$empleado["nombre"], $empleado["tipo"], $empleado["puntuacion"]]);
        }
        rewind($csv);
        $response->getBody()->write(stream_get_contents($csv));
        fclose($csv);
        return $response->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $puesto . '.csv"');
    }
    public function rotar(Request $request, Response $response, $args)
    {
        try {
            parse_str(file_get_contents('php://input'), $params);
            if (isset($params["id"], $params["nuevoPuesto"]) && !empty($params["id"] && !empty($params["nuevoPuesto"]))) {
                Empleado::rotarPersonal($params["id"], $params["nuevoPuesto"]);
                $response->getBody()->write(json_encode(["MENSAJE" => "el personal ha rotado"]));
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "faltan parametros"]));
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => "al rotar el personal"]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verPedidos(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        if (!empty($params["id"])) {
            $estado = Socio::verTiempoPedido($params["id"]);
            if (!empty($estado)) {
                if ($estado !== null) {
                    $response->getBody()->write(json_encode(["ESTADO" => $estado]));
                } else {
                    $response->getBody()->write(json_encode(["ESTADO" => "el empleado todabia no le asigno tiempo"]));
                }
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "id invalido"]));
            }
        } else {
            $pedidos = Socio::verPedido();
            if (!empty($pedidos)) {
                foreach ($pedidos as $pedido) {
                    $producto = Producto::obtenerProducto(intval($pedido["idProducto"]));
                    if (!empty($producto)) {

                        $response->getBody()->write(json_encode([
                            "id" => $pedido["id"],
                            "COMIDA" => $producto["nombre"],
                            "PRECIO" => $producto["precio"]
                        ]));
                    } else {
                        $response->getBody()->write(json_encode(["ERROR" => "ese producto esta eliminado"]));
                    }
                }
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "no hay pedidos"]));
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verMejorcomentario(Request $request, Response $response)
    {
        try {
            $resultado = Socio::verMejorcomentario();
            $response->getBody()->write(json_encode(["Mejores Comentarios" => $resultado]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function VerCancelados(Request $request, Response $response)
    {
        try {
            $resultados = Socio::VerCancelados();

            foreach ($resultados as $resultado) {
                $response->getBody()->write(json_encode(["PEDIDOS CANCELADOS" => ($resultado)]));
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verPedidosFueraTiempo(Request $request, Response $response)
    {
        try {
            $resultados = [];
            $pedidos = Socio::PedidosFueraTiempo();
            foreach ($pedidos as $pedido) {
                $producto = Producto::obtenerProducto($pedido["idProducto"]);

                $tiempoSegundos = abs(strtotime($pedido["fechaInicio"]) - strtotime($pedido["fechaEntrega"]));
                $tiempoMinutos = round($tiempoSegundos / 60);

                if ($tiempoMinutos > 59) {
                    $horas = intdiv($tiempoMinutos, 60);
                    $minutosRestantes = $tiempoMinutos % 60;
                    $tiempoDemora = "{$horas} horas y {$minutosRestantes} minutos";
                } else {
                    $tiempoDemora = "{$tiempoMinutos} minutos";
                }

                $resultados[] = [
                    "codigo del pedido" => $pedido["codigoAlfa"],
                    "Producto" => $producto["nombre"],
                    "TIEMPO DE DEMORA REAL" => $tiempoDemora,
                    "TIEMPO ESTIPULADO" => $pedido["tiempo"] . " minutos"
                ];
            }
            $response->getBody()->write(json_encode($resultados));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["ERROR" => $e->getMessage()]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function verLogo(Request $request, Response $response)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->Image("imagenes/LaComanda.png", 50, 10, 100);
        $pdf->SetFont('Arial', 'B', 16);
        $pdfOutput = $pdf->Output('S');
        $body = new \Slim\Psr7\Stream(fopen('php://memory', 'r+'));
        $body->write($pdfOutput);
        $body->rewind();
        return $response->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="estadisticas.pdf"')
            ->withBody($body);
    }
}

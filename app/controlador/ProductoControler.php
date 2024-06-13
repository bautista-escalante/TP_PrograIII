<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
include_once "modelo/Producto.php";
class ProductoControler {
    public function agregarProducto(Request $request, Response $response, $args) {
        $param = $request->getParsedBody();
        if (isset($param["nombre"], $param["puesto"], $param["precio"]) && 
            !empty($param["nombre"]) && !empty($param["puesto"]) && !empty($param["precio"])) {
            $producto = new Producto($param["nombre"], $param["puesto"], $param["precio"]);
            $producto->guardar();
            $response->getBody()->write("Producto agregado correctamente.<br>");
            return $response->withStatus(201); 
        } else {
            $response->getBody()->write("Error: agrega los atributos para dar el alta.<br>");
            return $response->withStatus(400); 
        }
    }

    public function borrarProducto(Request $request, Response $response, $args) {
        $id = $args['id'];
        if (!empty($id)) {
            Producto::eliminarProducto($id);
            $response->getBody()->write("Producto borrado correctamente.<br>");
            return $response->withStatus(200); 
        } else {
            $response->getBody()->write("Error: coloca los parámetros para borrar el producto.<br>");
            return $response->withStatus(400);
        }
    }

    public function modificarProducto(Request $request, Response $response, $args) {
        try {
            $id = $args['id'];
            $precio = $args['precio'];
            if (isset($id) && !empty($id) && isset($precio) && !empty($precio)) {
                Producto::modificarPrecioProducto($id, $precio);
                $response->getBody()->write("Precio actualizado.<br>");
                return $response->withStatus(200);
            } else {
                $response->getBody()->write("Error: coloca los parámetros para actualizar los precios.<br>");
                return $response->withStatus(400);
            }
        } catch (PDOException $e) {
            $response->getBody()->write("Error al actualizar precio: " . $e->getMessage() . "<br>");
            return $response->withStatus(500);
        }
    }

    public function listarProductos(Request $request, Response $response, $args) {
        $productos = Producto::mostrarProductos();
        foreach ($productos as $producto) {
            $response->getBody()->write("Nombre: " . $producto["nombre"] . "<br>Precio: " . $producto["precio"] . ".<br><br>");
        }
        return $response->withStatus(200);
    }
}


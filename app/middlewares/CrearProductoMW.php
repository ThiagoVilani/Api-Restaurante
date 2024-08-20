<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;

class CrearProductoMW
{
    /**
     * Example middleware invokable class
     *
     * @param  Request        $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return ResponseMW
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {
        $params = $request->getParsedBody();

        $validaciones = [
            'nombre' => 'Herramientas::ValidarPalabra',
            'tiempoPreparacion' => 'Herramientas::ValidarNumero',
            'precio' => 'Herramientas::ValidarNumero',
            'tipo' => 'Herramientas::ValidarTipoProducto',
            "stock" => "Herramientas::ValidarNumero"
        ];

        $mensajes = [
            'nombre' => 'Ingrese un nombre valido',
            'tiempoPreparacion' => 'Ingrese un tiempo valido',
            'precio' => 'Ingrese una precio valido',
            'tipo' => 'Ingrese un tipo valido',
            "stock" => "Ingrese un numero valido"
        ];

        foreach ($validaciones as $campo => $funcionDeValidacion) {
            if (!$funcionDeValidacion($params[$campo])) {
                $response = new ResponseMW();
                $response->getBody()->write($mensajes[$campo]);
                return $response;
            }
        }

        return $handler->handle($request);
    }
}
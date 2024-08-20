<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;

class CrearClienteMW
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
            'email' => 'Herramientas::ValidarEmail',
            'clave' => 'Herramientas::ValidarClave',
            'nombre' => 'Herramientas::ValidarPalabra',
            'dinero' => 'Herramientas::ValidarNumero'
        ];

        $mensajes = [
            'email' => 'Ingrese un email valido',
            'clave' => 'Ingrese una clave valida',
            'nombre' => 'Ingrese un nombre valido',
            'dinero' => 'Ingrese un monto valido'
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
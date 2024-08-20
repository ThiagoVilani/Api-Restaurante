<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;

class ValidarClaveMW
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
        if(Herramientas::ValidarClave($params["clave"])){
            $response = $handler->handle($request);
        }else{
            echo "Ingrese una clave valida";
            $response = new ResponseMW();
        }
        return $response;
    }
}
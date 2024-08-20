<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;

class ValidarTipoEmpleadoCocinaMW
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
        if($params["tipo"] == "cocinero" ||
            $params["tipo"] == "bartender" ||
            $params["tipo"] == "pastelero" ||
            $params["tipo"] == "cervezero"){
                return $handler->handle($request);
        }else{
            $response = new ResponseMW;
        }
        return $response;
    }
}
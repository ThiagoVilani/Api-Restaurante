<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class JwtMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler,$next): ResponseMW
    {
        $response = $handler->handle($request);
        $secret = "tu_secreto"; // Define tu secreto aquí

        $token = $request->getHeader('Authorization');
 
        if (!$token) {
            // return $response->withJson(['error' => 'Token not provided'], 401);
            // Me da error porque mi response no tiene el "with json"
        }

        try {
            $decoded = JWT::decode($token[0], new Key($secret, 'HS256'));
            $request = $request->withAttribute('jwt', $decoded);
        } catch (\Exception $e) {
            // return $response->withJson(['error' => 'Invalid token'], 401);
            // Me da error porque mi response no tiene el "with json"
        }

        $response = $next($request, $response);
        return $response;
    }
}
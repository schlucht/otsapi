<?php

declare(strict_types=1);

namespace Ots\API\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware
{
    private string $secret;

    public function __construct()
    {
        $this->secret = "Birgisch-3903-Schlucht"; //getenv('JWT_SECRET');
        if ($this->secret === false) {
            throw new \Exception("JWT_SECRET environment variable not set.");
        }
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Fehler Baeren: Authentication required'), 401);        
        }
        
        $token = substr($authHeader, 7);        
        if (empty($token)) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Fehler Token: Authentication required'), 401);
        }

        try {
            $payload = JWT::decode($token, new Key($this->secret, 'HS256'));
            $request = $request
            ->withAttribute('user_id', $payload->sub)
            ->withAttribute('user_email', $payload->email);
            return $handler->handle($request);
        } catch (ExpiredException $e) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Token expired'), 401);
        } catch (SignatureInvalidException $e) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Invalid token signature'), 401);
        } catch (\Exception $e) {
            error_log('Auth middleware error: ' . $e->getMessage());
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Invalid token'), 401);
        }
    }
}

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

class AuthMiddleware
{
    private string $secret;

    public function __construct()
    {
        $this->secret = getenv('JWT_SECRET');
        if ($this->secret === false) {
            throw new \Exception("JWT_SECRET environment variable not set.");
        }
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if (empty($token)) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Authentication required'), 401);
        }

        try {
            JWT::decode($token, new Key($this->secret, 'HS256'));
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Invalid token'), 401);
        }
    }
}

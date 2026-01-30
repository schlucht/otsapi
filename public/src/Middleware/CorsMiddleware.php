<?php

declare(strict_types=1);

namespace Ots\OTS\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class CorsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $origin = $request->getHeaderLine('Origin') ?: '*';
        $allowedOrigins = [
            'http://localhost:4200',
            'http://127.0.0.1:4200',
            'https://jagolo.ch',
            'https://schmidschlucht.ch',
            'https://4200-firebase-schlucht-1765795472802.cluster-64pjnskmlbaxowh5lzq6i7v4ra.cloudworkstations.dev/',
        ]; // anpassen
        $allowOrigin = in_array($origin, $allowedOrigins, true) ? $origin : '*';

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = new \Slim\Psr7\Response(204);
            return $response
                ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Max-Age', '86400');
        }

        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
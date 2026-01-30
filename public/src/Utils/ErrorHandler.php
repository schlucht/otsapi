<?php

declare(strict_types=1);

namespace Ots\OTS\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;

class ErrorHandler
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ?LoggerInterface $logger = null,
        private bool $includeDetailsInResponse = false
    ) {}

    public function handle(
        ServerRequestInterface $request,
        Throwable $exception
    ): ResponseInterface {
        $status = 500;
        $title = 'Internal Server Error';
        $detail = 'Something went wrong';

        if ($exception instanceof HttpException) {
            $status = $exception->getCode() ?: 500;
            $title = $exception->getMessage() ?: 'HTTP Error';
            $detail = 'Request could not be processed.';
        }

        $extra = [];
        if ($this->includeDetailsInResponse) {
            $extra = [
                'exception' => [
                    'type'    => get_class($exception),
                    'message' => $exception->getMessage(),
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'trace'   => array_slice($exception->getTrace(), 0, 5),
                ],
            ];
        }

        $response = $this->responseFactory->createResponse($status);
        $payload = [
            'error' => [
                'title'  => $title,
                'status' => $status,
                'detail' => $detail,
            ],
        ] + $extra;

        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}

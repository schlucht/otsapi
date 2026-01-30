<?php

declare(strict_types=1);

namespace Ots\API\Utils;

use Psr\Http\Message\ResponseInterface;

class ResponseHelper
{
    public function __construct(private ResponseInterface $response) {}

    /**
     * Schreibt JSON in den Response und gibt ihn zurück
     */
    public function write(ResponseMessage $message, int $status = 200): ResponseInterface
    {
        $this->response->getBody()->write(json_encode($message));
        return $this->response->withHeader('Content-Type', 'application/json')
                              ->withStatus($status);
    }
}
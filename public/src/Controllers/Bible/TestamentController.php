<?php

declare(strict_types=1);

namespace Ots\API\Controllers\Bible;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Repositories\Bible\TestamentRepository;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class TestamentController
{
    public function __construct(private TestamentRepository $repo) {}

    function allTestaments(Request $request, Response $response): Response
    {
        $responseHelper = new ResponseHelper($response);
        try {
            $res = $this->repo->allTestaments();
            $body = json_encode($res);
            return $responseHelper->write(new ResponseMessage(true, $body, null), 200);
        } catch (\PDOException $e) {
            return $responseHelper->write(new ResponseMessage(true, null, $e->getMessage()), 500);
        }
    }
}

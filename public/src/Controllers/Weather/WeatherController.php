<?php

declare(strict_types=1);

namespace Ots\API\Controllers\Weather;

use Ots\OTS\Database;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Repositories\Weather\WeatherRepository;
use Ots\API\Models\Weather\WeatherModel;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class WeatherController
{
    public function __construct(private WeatherRepository $repo) {}

    function index(Request $request, Response $response): Response    {
        $resp = new ResponseHelper($response);            
        try {
            $res = $this->repo->getWeathers();
            $body = json_encode($res);
            $message = new ResponseMessage(true, $body, null);
            return $resp->write($message, 200);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }
    }

    function store(Request $request, Response $response, array $args): Response
    {
        $resp = new ResponseHelper($response);  
        try {
            $data = $request->getParsedBody();
            $weather = new WeatherModel();
            $weather->temperature = isset($data['temperature']) ? (int)$data['temperature'] : 0;
            $weather->day = isset($data['day']) ?  new \DateTime($data['day']) : null;
            $weather->description = isset($data['description']) ? $data['description']  : '';
            $id = $this->repo->SetWeather($weather);
            $response->getBody()->write(json_encode(['id' => $id]));
            return $resp->write(new ResponseMessage(true, ['id' => $id], null), 201);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);    
        }
    }

    function show(Request $request, Response $response, array $args): Response    {
        $resp = new ResponseHelper($response);            
        try {
            $id = isset($args['id']) ? (int)$args['id'] : 0;
            $weather = $this->repo->findById($id);
            if ($weather) {
                $body = json_encode($weather);
                return $resp->write(new ResponseMessage(true, $body, null), 200);            
            } else {
                return $resp->write(new ResponseMessage(false, null, 'User not found'), 404);            
            }
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);    
        }
    }
}

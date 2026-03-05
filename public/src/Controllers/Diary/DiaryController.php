<?php

namespace Ots\API\Controllers\Diary;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Repositories\Diary\DiaryRepository;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class DiaryController{

    function __construct(private DiaryRepository $repo)
    {        
        
    }

    function index(Request $request, Response $response): Response{  
        $resp = new ResponseHelper($response);
        try {
            $userId = $request->getAttribute('user_id');
            if (!is_int($userId) || $userId <= 0) {
                return $resp->write(new ResponseMessage(false, null, 'Unauthorized'), 401);
            }

            $res = $this->repo->getDiaryEntries($userId);
            $message = new ResponseMessage(true, $res, null);
            return $resp->write($message, 200);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }
    }

    function store(Request $request, Response $response): Response{
        $resp = new ResponseHelper($response);
        try {
            $userId = $request->getAttribute('user_id');
            if (!is_int($userId) || $userId <= 0) {
                return $resp->write(new ResponseMessage(false, null, 'Unauthorized'), 401);
            }

            $data = $request->getParsedBody();
            var_dump($data);
            if (empty($data['description']) || !is_string($data['description'])) {
                return $resp->write(new ResponseMessage(false, null, 'Content is required and must be a string'), 400);
            }

            $entryId = $this->repo->createDiaryEntry(
                $userId
                , $data['description']
                , $data['weather'] ?? null
                , $data['weight'] ?? 0
                , $data['temperature'] ?? null
                );
            return $resp->write(new ResponseMessage(true, ['id' => $entryId], null), 201);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }
    }
}

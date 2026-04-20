<?php

namespace Ots\API\Controllers\Book;

use Ots\API\Repositories\Book\GenresRepositiory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class GenresController {

    public function __construct(private GenresRepositiory $repo)
    {        
    }    
    public function index() {        
    }

    function store(Request $request, Response $response): Response{
           $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();            
            if (empty($data['genre']) || !is_string($data['genre'])) {
                return $resp->write(new ResponseMessage(false, null, 'Genre is required and must be a string'), 400);
            }

            $entryId = $this->repo->createGenre(            
                 $data['genre']
                , $data['description'] ?? null                
                );               
            return $resp->write(new ResponseMessage(true, ['id' => $entryId], null), 201);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }        
    }
}
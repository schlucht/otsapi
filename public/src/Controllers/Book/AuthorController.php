<?php

namespace Ots\API\Controllers\Book;

use Ots\API\Repositories\Book\AuthorRepositiory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class AuthorController {

    public function __construct(private AuthorRepositiory $repo)
    {        
    }    
    public function index(Request $request, Response $response): Response{  
        $resp = new ResponseHelper($response);            
        try {
            $res = $this->repo->getAllAuthor();
            $message = new ResponseMessage(true, $res, null);
            return $resp->write($message, 200);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }
    }     

    function store(Request $request, Response $response): Response{
           $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();            
            if (empty($data['lastname']) || !is_string($data['lastname'])) {
                return $resp->write(new ResponseMessage(false, null, 'Lastname is required and must be a string'), 400);
            }

            $entryId = $this->repo->createAuthor(            
                 $data['firstname']
                , $data['lastname']                
                , $data['country'] ?? ""                
                , $data['description'] ?? ""                
                );               
            return $resp->write(new ResponseMessage(true, ['id' => $entryId], null), 201);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }        
    }
}
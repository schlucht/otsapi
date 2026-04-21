<?php

namespace Ots\API\Controllers\Book;

use Ots\API\Repositories\Book\AuthorRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class AuthorController {

    public function __construct(private AuthorRepository $repo)
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

    function show(Request $request, Response $response, array $args): Response{
        $resp = new ResponseHelper($response);
        try {
            $id = isset($args['id']) ? (int)$args['id'] : 0;
            $entry = $this->repo->findById($id);
            if (empty($entry)) {
                return $resp->write(new ResponseMessage(false, null,'Author not found!'),404);
            } else {
                return $resp->write(new ResponseMessage(true, $entry,'User gefunden!'),200);
            }
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);
        }
    }

    function update(Request $request, Response $response, array $args): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $id = isset($args['id']) ? (int) $args['id'] : 0;
            if ($id <= 0) {
                return $resp->write(new ResponseMessage(false, null, 'Invalid author id'), 400);
            }

            $entry = $this->repo->findById($id);
            if (empty($entry)) {
                return $resp->write(new ResponseMessage(false, null, 'Author not found!'), 404);
            }

            $data = $request->getParsedBody() ?? [];
            $entry->firstname = isset($data['firstname']) && is_string($data['firstname']) ? $data['firstname'] : $entry->firstname;
            $entry->lastname = isset($data['lastname']) && is_string($data['lastname']) ? $data['lastname'] : $entry->lastname;
            $entry->country = isset($data['country']) && is_string($data['country']) ? $data['country'] : $entry->country;
            $entry->description = isset($data['description']) && is_string($data['description']) ? $data['description'] : $entry->description;

            $updated = $this->repo->updateAuthor($entry);
            return $resp->write(new ResponseMessage(true, $updated, null), 200);
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);
        }
    }

    function delete(Request $request, Response $response, array $args): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $id = isset($args['id']) ? (int) $args['id'] : 0;
            if ($id <= 0) {
                return $resp->write(new ResponseMessage(false, null, 'Invalid author id'), 400);
            }

            $deleted = $this->repo->deleteAuthor($id);
            if (!$deleted) {
                return $resp->write(new ResponseMessage(false, null, 'Author not found!'), 404);
            }

            return $resp->write(new ResponseMessage(true, null, null), 200);
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);
        }
    }
}
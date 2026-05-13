<?php

declare(strict_types=1);

namespace Ots\API\Controllers\Bible;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Repositories\Bible\BookRepository;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;

class BookController
{
    public function __construct(private BookRepository $repo){}

    function allBooks(Request $request, Response $response): Response{
        $resp = new ResponseHelper($response);
        $message = new ResponseMessage(true);
        try{
            $res = $this->repo->getAllBooks();
            $body = json_encode($res);
            $response->getBody()->write($body);
            $message->data = $body;
            return $resp->write($message, 200);   
        } catch (\PDOException $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);  
        }
    }

    function booksFromTestament(Request $request, Response $response, array $args): Response{
        $resp = new ResponseHelper($response);        
        $message = new ResponseMessage(true);
        try {           
            $res = $this->repo->getBooksFromTestament($args['testament']);
            $body = json_encode($res);
            $response->getBody()->write($body);
            $message->data = $body;
            return $resp->write($message, 200);   
        } catch (\PDOException $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);  
        }
    }
    function bookFromBook(Request $request, Response $response, array $args): Response{
        $resp = new ResponseHelper($response);        
        $message = new ResponseMessage(true);
        try {           
            $res = $this->repo->getBookFromBook($args['book']);
            $body = json_encode($res);
            $response->getBody()->write($body);
            $message->data = $body;
            return $resp->write($message, 200);   
        } catch (\PDOException $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);  
        }
    }
}
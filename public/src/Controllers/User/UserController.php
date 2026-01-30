<?php

declare(strict_types=1);

namespace Ots\OTS\Controllers\User;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\OTS\Repositories\User\UserRepository;
use Ots\OTS\Utils\ResponseHelper;
use Ots\OTS\Utils\ResponseMessage;

class UserController
{
    

    public function __construct(private UserRepository $repo)
    {        
        
    }

    function allUsers(Request $request, Response $response): Response{  
        $resp = new ResponseHelper($response);            
        try {
            $res = $this->repo->getUsers();
            $body = json_encode($res);
            $message = new ResponseMessage(true, $body, null);
            return $resp->write($message, 200);            
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);            
        }
    }

    function userById(Request $request, Response $response, array $args): Response
    {
        $resp = new ResponseHelper($response);  
        try {
            // $data = $request->getParsedBody();
            // $id = isset($data['id']) ? (int)$data['id'] : 0;
            $id = isset($args['id']) ? (int)$args['id'] : 0;
            $user = $this->repo->findById($id);
            if ($user) {
                $body = json_encode($user);
                return $resp->write(new ResponseMessage(true, $body, null), 200);            
            } else {
                return $resp->write(new ResponseMessage(false, null, 'User not found'), 404);            
            }
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);    
        }
    }
    function delete(Request $request, Response $response, array $args): Response
    {
        $resp = new ResponseHelper($response);  
        try {
            $data = $request->getParsedBody();
            $id = isset($data['id']) ? (int)$data['id'] : 0;
            $user = $this->repo->delete($id);
            if ($user) {
                $body = json_encode($user);
                return $resp->write(new ResponseMessage(true, $body, null), 200);            
            } else {
                return $resp->write(new ResponseMessage(false, null, 'User not found'), 404);            
            }
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);    
        }
    }  
}
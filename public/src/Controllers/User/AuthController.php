<?php

declare(strict_types=1);

namespace Ots\API\Controllers\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;
use Ots\API\Repositories\User\UserRepository;

use Ots\API\Models\User\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private string $secret;

    public function __construct(private UserRepository $repo) {
        $this->secret = getenv('JWT_SECRET');
        if ($this->secret === false) {
            throw new \Exception("JWT_SECRET environment variable not set.");
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();
            $user = new UserModel();
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];
            $user->email = $data['email'];
            $user->password = $data['password']; // wird im Repo gehasht

            $id = $this->repo->createUser($user);
            return $resp->write(new ResponseMessage(true, ['id' => $id]));
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);
        }
    }

    public function login(Request $request, Response $response): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();
            $user = $this->repo->findByEmail($data['email']);

            if ($user && password_verify($data['password'], $user->password)) {
                $payload = [
                    'sub' => $user->id,
                    'email' => $user->email,
                    'iat' => time(),
                    'exp' => time() + 3600
                ];
                $token = JWT::encode($payload, $this->secret, 'HS256');

                $resp = new ResponseHelper($response);
                return $resp->write(new ResponseMessage(true, ['token' => $token]));
            }
            return $resp->write(new ResponseMessage(false, null, 'Invalid credentials'), 401);
        } catch (\Exception $e) {
            return $resp->write(new ResponseMessage(false, null, $e->getMessage()), 500);
        }
    }

    public function me(Request $request, Response $response): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $payload = JWT::decode($token, new Key($this->secret, 'HS256'));
            $user = $this->repo->findById($payload->sub);

            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(true, $user));
        } catch (\Exception $e) {
            $resp = new ResponseHelper($response);
            return $resp->write(new ResponseMessage(false, null, 'Invalid token'), 401);
        }
    }
}

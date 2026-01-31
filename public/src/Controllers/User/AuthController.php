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
use Ots\API\Utils\Validator;

class AuthController
{
    private string $secret;

    public function __construct(private UserRepository $repo) {
        $this->secret = "Birgisch-3903-Schlucht"; //getenv('JWT_SECRET');
        if ($this->secret === false) {            
            throw new \Exception("JWT_SECRET environment variable not set.");
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();
            $missing = Validator::validateRequired($data, ['firstname', 'lastname', 'email', 'password']);
            if (!empty($missing)) {
                return $resp->write(
                    new ResponseMessage(false, null, 'Missing required fields: ' . implode(', ', $missing)),
                    400
                );
            }

            // Validate email format
            if (!Validator::validateEmail($data['email'])) {
                return $resp->write(
                    new ResponseMessage(false, null, 'Invalid email format'),
                    400
                );
            }

            // Validate password strength
            $passwordErrors = Validator::validatePassword($data['password']);
            if (!empty($passwordErrors)) {
                return $resp->write(
                    new ResponseMessage(false, null, implode(', ', $passwordErrors)),
                    400
                );
            }

            // Check if email already exists
            if ($this->repo->findByEmail($data['email']) !== null) {
                return $resp->write(
                    new ResponseMessage(false, null, 'Email already registered'),
                    409
                );
            }
            $user = new UserModel();
            $user->firstname = Validator::sanitizeString($data['firstname']);
            $user->lastname = Validator::sanitizeString($data['lastname']);
            $user->email = Validator::sanitizeString($data['email']);            
            $user->password = $data['password']; // wird im Repo gehasht

            $id = $this->repo->createUser($user);
            return $resp->write(new ResponseMessage(true, ['id' => $id, 'message' => 'User registered successfully']));
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return $resp->write(new ResponseMessage(false, null, 'Registration failed'), 500);
        }
    }

    public function login(Request $request, Response $response): Response
    {
        $resp = new ResponseHelper($response);
        try {
            $data = $request->getParsedBody();
            $missing = Validator::validateRequired($data, ['email', 'password']);
            if (!empty($missing)) {
                return $resp->write(
                    new ResponseMessage(false, null, 'Missing required fields'),
                    400
                );
            }
            $user = $this->repo->findByEmail($data['email']);

            if ($user && password_verify($data['password'], $user->password)) {
                $payload = [
                    'sub' => $user->id,
                    'email' => $user->email,
                    'iat' => time(),
                    'exp' => time() + 3600
                ];
                $token = JWT::encode($payload, $this->secret, 'HS256');

                return $resp->write(new ResponseMessage(true, [
                    'token' => $token,
                    'expiresIn' => 3600,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname
                    ]
                ]));
            }
            return $resp->write(new ResponseMessage(false, null, 'Invalid credentials'), 401);
        } catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return $resp->write(new ResponseMessage(false, null, 'Authentication failed'), 500);
        }
    }

    public function me(Request $request, Response $response): Response
    { 
        $resp = new ResponseHelper($response);
        try {
            // Get user ID from request attribute (set by AuthMiddleware)
            $userId = $request->getAttribute('user_id');
            
            if (!$userId) {
                return $resp->write(new ResponseMessage(false, null, 'Unauthorized'), 401);
            }

            $user = $this->repo->findById($userId);
            
            if (!$user) {
                return $resp->write(new ResponseMessage(false, null, 'User not found'), 404);
            }
            return $resp->write(new ResponseMessage(true, $user));
        } catch (\Exception $e) {
            error_log('Me endpoint error: ' . $e->getMessage());
            return $resp->write(new ResponseMessage(false, null, 'Failed to retrieve user'), 500);
        }
    }
}

<?php

declare(strict_types=1);

namespace Ots\API\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Ots\API\Utils\ResponseHelper;
use Ots\API\Utils\ResponseMessage;
use Ots\API\Database;

/**
 * Database-backed rate limiting middleware
 * 
 * Usage in routes.php:
 * $app->post('/api/auth/login', [AuthController::class, 'login'])
 *     ->add(new RateLimitMiddleware($database, 5, 300)); // 5 requests per 5 minutes
 */
class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $decaySeconds;
    private Database $database;
    private ?\PDO $pdo = null;
    private bool $dbAvailable = true;

    /**
     * @param Database $database Database connection
     * @param int $maxAttempts Maximum number of requests allowed
     * @param int $decaySeconds Time window in seconds
     */
    public function __construct(Database $database, int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        $this->database = $database;
    }
    
    private function getConnection(): ?\PDO
    {
        if (!$this->dbAvailable) {
            return null;
        }
        
        if ($this->pdo !== null) {
            return $this->pdo;
        }
        
        try {
            $this->pdo = $this->database->getConnection();
            return $this->pdo;
        } catch (\Exception $e) {
            error_log('Rate limit: Database connection failed - ' . $e->getMessage());
            $this->dbAvailable = false;
            return null;
        }
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Wenn DB nicht verfügbar, Request durchlassen (fail-open)
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return $handler->handle($request);
        }
        
        $identifier = $this->getIdentifier($request);
        $key = $this->generateKey($identifier);
        
        $attempts = $this->getAttempts($key);
        $this->cleanupOldAttempts($attempts);
        
        if (count($attempts) >= $this->maxAttempts) {
            $response = new \Slim\Psr7\Response();
            $resp = new ResponseHelper($response);
            
            $oldestAttempt = min($attempts);
            $retryAfter = $this->decaySeconds - (time() - $oldestAttempt);
            
            $response = $resp->write(
                new ResponseMessage(false, null, 'Too many requests. Please try again later.'),
                429
            );
            
            return $response->withHeader('Retry-After', (string)max(1, $retryAfter));
        }
        
        // Record this attempt
        $attempts[] = time();
        $this->saveAttempts($key, $attempts);
        
        return $handler->handle($request);
    }

    private function getIdentifier(Request $request): string
    {
        // Use IP address as base identifier
        $serverParams = $request->getServerParams();
        $ip = $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Check for forwarded IP (if behind proxy)
        if (isset($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIps = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwardedIps[0]);
        }
        
        $path = $request->getUri()->getPath();
        
        // For login/register endpoints, include email in identifier
        // This prevents one user from blocking others on the same IP
        $email = '';
        if (in_array($path, ['/api/auth/login', '/api/auth/register'])) {
            $body = $request->getParsedBody();
            if (isset($body['email']) && is_string($body['email'])) {
                $email = strtolower(trim($body['email']));
            }
        }
        
        return $email ? "{$ip}:{$path}:{$email}" : "{$ip}:{$path}";
    }

    private function generateKey(string $identifier): string
    {
        return md5($identifier);
    }

    private function getAttempts(string $key): array
    {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return [];
        }
        
        try {
            // Cleanup old entries first
            $cutoff = date('Y-m-d H:i:s', time() - $this->decaySeconds);
            $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE updated_at < :cutoff");
            $stmt->execute(['cutoff' => $cutoff]);
            
            // Get current attempts
            $stmt = $pdo->prepare("SELECT attempts FROM rate_limits WHERE identifier = :key");
            $stmt->execute(['key' => $key]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                return json_decode($result['attempts'], true) ?? [];
            }
            return [];
        } catch (\PDOException $e) {
            error_log('Rate limit DB error: ' . $e->getMessage());
            return [];
        }
    }

    private function saveAttempts(string $key, array $attempts): void
    {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            return;
        }
        
        try {
            $json = json_encode($attempts);
            $stmt = $pdo->prepare(
                "INSERT INTO rate_limits (identifier, attempts, updated_at) 
                 VALUES (:key, :attempts, NOW()) 
                 ON DUPLICATE KEY UPDATE attempts = :attempts, updated_at = NOW()"
            );
            $stmt->execute([
                'key' => $key,
                'attempts' => $json
            ]);
        } catch (\PDOException $e) {
            error_log('Rate limit DB save error: ' . $e->getMessage());
        }
    }

    private function cleanupOldAttempts(array &$attempts): void
    {
        $cutoff = time() - $this->decaySeconds;
        $attempts = array_filter($attempts, fn($timestamp) => $timestamp > $cutoff);
    }
}

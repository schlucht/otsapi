<?php

declare(strict_types=1);

namespace Ots\API;

use PDO;

class Database{
    private ?PDO $connection = null;
    
    public function getConnection():PDO
    {
        // Lazy Loading: Verbindung nur aufbauen wenn wirklich benötigt
        if ($this->connection !== null) {
            return $this->connection;
        }
        
        try {
        $host = defined('DB_HOST') ? DB_HOST : 'db';
        $dbname = defined('DB_NAME') ? DB_NAME : 'schlucht';
        $user = defined('DB_USER') ? DB_USER : 'schlucht';
        $pass = defined('DB_PASS') ? DB_PASS : 'schlucht';
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";

        $this->connection = new PDO($dsn, $user, $pass);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this->connection;
        } catch (\PDOException $e) {
            // Im Produktivmodus keine Details preisgeben
            $message = defined('APP_ENV') && APP_ENV === 'dev' 
                ? 'Database connection error: ' . $e->getMessage()
                : 'Database connection error';
            throw new \RuntimeException($message, 500);
        }
    }
}
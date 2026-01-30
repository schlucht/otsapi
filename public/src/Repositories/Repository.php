<?php

declare(strict_types=1);

namespace Ots\API\Repositories;

use DateTime;
use Ots\API\Database;
use PDO;

class Repository
{
    protected string $table;
    protected PDO $pdo;
    private Database $database;

    public function __construct(Database $database) {       
        $this->database = $database;
    }

    public function getAll(): array 
    {   
        $this->pdo = $this->database->getConnection();
        $sql = "SELECT * FROM $this->table";
        $stmt = $this->pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
    
}

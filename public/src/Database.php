<?php

declare(strict_types=1);

namespace Ots\OTS;

use PDO;

class Database{
    public function getConnection():PDO
    {
        $dsn = "mysql:host=db;dbname=schlucht;charset=utf8";

        $pdo = new PDO($dsn, "schlucht", "schlucht");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
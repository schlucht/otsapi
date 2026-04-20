<?php

namespace Ots\API\Repositories\Book;

use PDO;
use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\Api\Models\Book\GenreModel;

class GenresRepositiory extends Repository {
    public function __construct(private Database $database) {
        parent::__construct($database);
        $this->table = "genres";
    }

    public function createGenre($genre, $genreDesc): int{
        try{
            $this->pdo = $this->database->getConnection();
            $sql = "INSERT INTO genres (genre, description ) VALUES (:genre, :description)";          
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindValue(':description', $genreDesc, PDO::PARAM_STR);

            $stmt->execute();
            return (int)$this->pdo->lastInsertId();

        }
        catch (\PDOException $e) {
            throw $e;
        }
    }
}
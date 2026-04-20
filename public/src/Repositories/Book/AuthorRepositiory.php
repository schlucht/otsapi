<?php

namespace Ots\API\Repositories\Book;

use PDO;
use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\API\Models\Book\AuthorModel;

class AuthorRepositiory extends Repository {
    public function __construct(private Database $database) {
        parent::__construct($database);
        $this->table = "authors";
    }

    public function getAllAuthor() {        
          try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT author_id, firstname, lastname, country, description, created_at, updated_at FROM $this->table";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $authors = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $author = new AuthorModel();
                $author->authorId = (int)$row['author_id'];
                $author->firstname = $row['firstname'];
                $author->lastname = $row['lastname'];
                $author->country = $row['country'];
                $author->description = $row['description'];
                $author->created = new \DateTime($row['created_at']);
                $author->updated = new \DateTime($row['updated_at']);
                $authors[] = $author;
            }
            return $authors;
        } catch (\PDOException $e) {
            throw $e;
        }

    }

    public function createAuthor($firstname, $lastname, $country="", $description=""): int{
        try{
            $this->pdo = $this->database->getConnection();
            $sql = "INSERT INTO $this->table (firstname, lastname, country, description ) VALUES (:firstname, :lastname, :country, :description)";          
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindValue(':country', $country, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);

            $stmt->execute();
            return (int)$this->pdo->lastInsertId();

        }
        catch (\PDOException $e) {
            throw $e;
        }
    }
}
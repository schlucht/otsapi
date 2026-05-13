<?php

declare(strict_types=1);

namespace Ots\API\Repositories\Bible;

use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\API\Models\Bible\BookModel;
use DateTime;
use PDO;

class BookRepository extends Repository
{
    public function __construct(private Database $database)
    {
        parent::__construct($database);
        $this->table = "biblebooks";        
    } 
    
    public function getAllBooks() : array
    {
        $this->pdo = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->table}";        
        $stmt = $this->pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $books = [];
        foreach($res as $t) {
            $book = new BookModel();            
            $book->id = (int)$t['id'];
            $book->name = $t['name'];
            $book->alternativeNames = explode(';', $t['alternativeNames'] ?? '');
            $book->testament = $t['testament'];
            $book->abbreviation = $t['abbreviation'];
            $book->author = $t['author'] ?? '';
            $book->year = $t['year'] ?? '';
            $book->description = $t['description'] ?? '';
            $book->createdAt = new DateTime($t['created_at']);
            $book->updatedAt = $t['updated_at'] ?? new DateTime();
            array_push($books, $book);
        }        
        return $books;
    }

    public function getBooksFromTestament(string $testament) : array
    {        
        $this->pdo = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->table} WHERE testament LIKE :testament"; 
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':testament', $testament, PDO::PARAM_STR);       
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $books = [];        
        foreach($res as $t) {
            $book = new BookModel();            
            $book->id = (int)$t['id'];
            $book->name = $t['name'];
            $book->alternativeNames = explode(';', $t['alternativeNames'] ?? '');
            $book->testament = $t['testament'];
            $book->abbreviation = $t['abbreviation'];
            $book->author = $t['author'] ?? '';
            $book->year = $t['year'] ?? '';
            $book->description = $t['description'] ?? '';
            $book->createdAt = new DateTime($t['created_at']);
            $book->updatedAt = $t['updated_at'] ?? new DateTime();
            array_push($books, $book);
        }        
        return $books;        
    }
    public function getBookFromBook(string $book) : ?BookModel
    {        
        $this->pdo = $this->database->getConnection();
        $sql = "SELECT * FROM {$this->table} WHERE alternativeNames LIKE :book"; 
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':book','%' .  $book . '%', PDO::PARAM_STR);       
        $stmt->execute();
        $t = $stmt->fetch(PDO::FETCH_ASSOC);            
        if($t) {
            $book = new BookModel();            
            $book->id = (int)$t['id'];
            $book->name = $t['name'];
            $book->alternativeNames = explode(';', $t['alternativeNames'] ?? '');
            $book->testament = $t['testament'];
            $book->abbreviation = $t['abbreviation'];
            $book->author = $t['author'] ?? '';
            $book->year = $t['year'] ?? '';
            $book->description = $t['description'] ?? '';
            $book->createdAt = new DateTime($t['created_at']);
            $book->updatedAt = $t['updated_at'] ?? new DateTime();
            return $book; 
        }    
        return null;    
    }
}
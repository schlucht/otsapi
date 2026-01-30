<?php

declare(strict_types=1);

namespace Ots\API\Repositories\Bible;

use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\API\Models\Bible\BookModel;
use Ots\API\Models\Bible\TestamentModel;
use DateTime;
use PDO;

class BookRepository extends Repository
{
    public function __construct(private Database $database)
    {
        parent::__construct($database);
        $this->table = "book";        
    } 
    
    public function getAllBooks() : array
    {
        $this->pdo = $this->database->getConnection();
        $sql = "SELECT b.*, t.id as t_id, t.name as t_name FROM book b JOIN testament t ON b.testament_id = t.id";        
        $stmt = $this->pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $books = [];
        foreach($res as $t) {
            $testament = new TestamentModel();
            $book = new BookModel();
            $testament->id = $t['t_id'];
            $testament->name = $t['t_name'];
            $book->testament = $testament;
            $book->id = $t['id'];
            $book->name = $t['name'];
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
    

}
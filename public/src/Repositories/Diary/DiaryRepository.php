<?php

declare(strict_types=1);

namespace Ots\API\Repositories\Diary;

use Ots\API\Repositories\Repository;
use Ots\API\Database;
use Ots\API\Models\Diary\DiaryModel;

class DiaryRepository extends Repository{

    function __construct(private Database $database)
    {
        parent::__construct($database);
        $this->table = "diary";
    }

    function getDiaryEntries(int $userId): array{
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT * FROM $this->table WHERE user_id = :user_id ORDER BY day DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            $diarys = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $diary = new DiaryModel();
                $diary->id = (int)$row['id'];
                $diary->userId = (int)$row['user_id'];
                $diary->day = new \DateTime($row['day']);
                $diary->weather = $row['weather'];
                $diary->weight = (float)$row['weight'];
                $diary->description = $row['description'];
                $diary->temperature = (float)$row['temperature'];
                $diary->created = new \DateTime($row['created_at']);
                $diary->updated = new \DateTime($row['updated_at']);
                $diarys[] = $diary;
            }
            return $diarys;
        } catch (\PDOException $e) {
            throw $e;
        }
    }   

    function createDiaryEntry(int $userId, string $content, ?string $weather = null, ?float $weight = 0, ?float $temperature = null): int{
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "INSERT INTO $this->table (user_id, day, weather, weight, temperature, description) VALUES (:user_id, :day, :weather, :weight, :temperature, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->bindValue(':day', (new \DateTime())->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
            $stmt->bindValue(':weather', $weather, \PDO::PARAM_STR);
            $stmt->bindValue(':weight', $weight, \PDO::PARAM_STR);
            $stmt->bindValue(':temperature', $temperature, \PDO::PARAM_STR);
            $stmt->bindValue(':description', $content, \PDO::PARAM_STR);
            $stmt->execute();
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw $e;
        }
    }

}
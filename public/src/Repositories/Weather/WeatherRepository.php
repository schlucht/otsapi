<?php

declare(strict_types=1);

namespace Ots\API\Repositories\Weather;

use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\API\Models\Weather\WeatherModel;

use DateTime;
use PDO;

class weatherRepository extends Repository
{
    public function __construct(private Database $database)
    {
        parent::__construct($database);
        $this->table = "weather";
    }

    public function getWeathers(): array
    {
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT id, temperature, `day`, `description`, insert_at FROM $this->table";
            $stmt = $this->pdo->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $weathers = [];
            foreach ($res as $t) {
                $weather = new WeatherModel();

                $weather->id = $t['id'];
                $weather->temperature = $t['temperature'];
                $weather->day = new DateTime($t['day']) ?? null;
                $weather->description = $t['description'] ?? '';
                $weather->insertAt = new DateTime($t['insert_at']);
                array_push($weathers, $weather);
            }
            return $weathers;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function SetWeather(WeatherModel $weather): int
    {
        try{
        $this->pdo = $this->database->getConnection();
        $sql = "INSERT INTO $this->table (temperature, `day`, `description`, insert_at) 
                VALUES (:temperature, :day, :description, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':temperature', $weather->temperature, PDO::PARAM_INT);
        $stmt->bindValue(':day', $weather->day->format('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(':description', $weather->description, PDO::PARAM_STR);

        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function  findById(int $id): ?WeatherModel
    {
        try{
            
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT id, temperature, `day`, `description`, insert_at FROM $this->table WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($res);
            if ($res) {
                $weather = new WeatherModel();
                $weather->id = (int)$res['id'];
                $weather->temperature = (int)$res['temperature'];
                $weather->day = $res['day'];
                $weather->description = $t['description'] ?? '';
                $weather->insertAt = $res['insert_at'];
                return $weather;
            }           
        
            return null;

        } catch(\PDOException $e){
            throw $e;
        }
    }
}

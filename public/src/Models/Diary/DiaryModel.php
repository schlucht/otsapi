<?php

namespace Ots\API\Models\Diary;

use DateTime;

class DiaryModel{
    public int $id;
    public int $userId;
    public DateTime $day;
    public string $weather;
    public float $weight;
    public string $description;
    public float $temperature;
    public DateTime $created;
    public DateTime $updated;
}
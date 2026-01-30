<?php

declare(strict_types=1);

namespace Ots\API\Models\Bible;

use DateTime;

class BookModel 
{
    public int $id;    
    public TestamentModel $testament;
    public string $name;
    public string $abbreviation;
    public string $author;
    public string $year;
    public string $description;
    public DateTime $createdAt;
    public DateTime $updatedAt;  
}


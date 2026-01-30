<?php

declare(strict_types=1);

namespace Ots\OTS\Models\Bible;

use DateTime;
use Ots\OTS\Repositories\Bible\BookRepository;
use Ots\OTS\Repositories\Bible\TestamentRepository;
use Ots\OTS\Repositories\Repository;
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


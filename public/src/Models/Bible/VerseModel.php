<?php

declare(strict_types=1);

namespace Ots\API\Models\Bible;

use DateTime;

class VerseModel 
{
    public int $id;
    public int $chapterId;
    public int $number;    
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
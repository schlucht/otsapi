<?php

declare(strict_types=1);

namespace Ots\API\Models\Bible;

use DateTime;

class TranslationModelModel 
{
    public int $id;   
    public string $name;
    public string $language;
    public string $description;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
<?php

declare(strict_types=1);

namespace Ots\API\Models\Bible;

use DateTime;

class TestamentModel 
{
    public int $id;
    public string $name;
    public DateTime $createdAt;
    public DateTime $updatedAt;

}
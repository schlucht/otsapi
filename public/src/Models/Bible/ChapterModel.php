<?php

declare(strict_types=1);

namespace Ots\OTS\Models\Bible;

use DateTime;

class ChapterModel 
{
    public int $id;
    public int $number;
    public TestamentModel $testament;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
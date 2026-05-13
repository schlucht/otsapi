<?php

declare(strict_types=1);

namespace Ots\API\Models\Bible;

use DateTime;

class VerseCommentModel 
{
    public int $id;
    public int $verseId;
    public int $userId;
    public string $text;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
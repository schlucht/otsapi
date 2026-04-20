<?php

namespace Ots\API\Models\Book;

use DateTime;

class GenreModel {

    public int $GenreId;
    public string $genre;
    public string $description;
    public DateTime $created;
    public DateTime $updated;
}
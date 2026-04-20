<?php

namespace Ots\API\Models\Book;

use DateTime;

class BookModel {
    public int $bookId;
    public string $isbn;
    public string $title;
    public string $subtitle;
    public int $author_id;
    public int $genre_id;
    public string $description;
    public  DateTime $created;
    public DateTime $updated;

}
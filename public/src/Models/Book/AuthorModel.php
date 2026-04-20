<?php

namespace Ots\API\Models\Book;

use DateTime;

class AuthorModel {

    public int $authorId;
    public string $firstname;
    public string $lastname;
    public string $country;
    public string $description;
    public DateTime $created;
    public DateTime $updated;
}
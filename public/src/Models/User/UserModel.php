<?php

declare(strict_types=1);

namespace Ots\OTS\Models\User;

use DateTime;

class UserModel 
{
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;
    public DateTime $updated;
    public DateTime $created; 

}
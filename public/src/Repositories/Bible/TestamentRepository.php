<?php

declare(strict_types=1);

namespace Ots\API\Repositories\Bible;

use Ots\API\Database;
use Ots\API\Repositories\Repository;
use Ots\API\Models\Bible\TestamentModel;
use DateTime;

class TestamentRepository extends Repository
{   
    public function __construct(Database $database)
    {
        parent::__construct($database);
        $this->table = "testament";
    }    
    function allTestaments(): array {
        $res =  $this->getAll();       
        $testaments = [];
        foreach($res as $t) {
            $testament = new TestamentModel();
            $testament->id = $t['id'];
            $testament->name = $t['name'];
            $testament->createdAt = new DateTime($t['created_at']);
            $testament->updatedAt = $t['updated_at'] ?? new DateTime();
            array_push($testaments, $testament);
        }
        return $testaments;
    }
}
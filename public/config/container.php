<?php

declare(strict_types=1);

use Ots\API\Database;

use function DI\create;

return [
    Database::class => create(Database::class),
];
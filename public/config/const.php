<?php

declare(strict_types=1);

// Umgebung: 'dev' oder 'prod'
define('APP_ENV', 'dev');

// Datenbank-Konfiguration
define('DB_HOST', 'db');
define('DB_NAME', 'schlucht');
define('DB_USER', 'schlucht');
define('DB_PASS', 'schlucht');

define('JWT_SECRET', 'Absolut-Geheim-dann-mache-ich-ihn-halt-länger'); // In Produktion sollte dies aus einer sicheren Quelle stammen, z.B. Umgebungsvariable
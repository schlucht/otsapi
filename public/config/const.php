<?php

declare(strict_types=1);

// Umgebung: 'dev' oder 'prod'
define('APP_ENV', 'dev');

// Datenbank-Konfiguration
define('DB_HOST', 'db55.hostpark.net');
define('DB_NAME', 'schmidschluch1');
define('DB_USER', 'schmidschluch1');
define('DB_PASS', 'Schlucht6');

define('JWT_SECRET', 'Absolut-Geheim-dann-mache-ich-ihn-halt-länger'); // In Produktion sollte dies aus einer sicheren Quelle stammen, z.B. Umgebungsvariable
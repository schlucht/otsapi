<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Ots\API\Middleware\AddJsonResponseHeader;
use Slim\Factory\AppFactory;
use \Ots\API\Middleware\CorsMiddleware;
   
require __DIR__ . '/vendor/autoload.php';


// Optionale lokale Konfiguration (wird nicht ins Git committed)
if (file_exists(__DIR__ . '/config/config_local.php')) {
    require __DIR__ . '/config/config_local.php';
} else {
    // Datei nicht vorhanden, Standardwerte aus const.php werden verwendet
    require __DIR__ . '/config/const.php';
}

// Environment: 'dev' oder 'prod'
$environment = defined('APP_ENV') ? APP_ENV : 'prod';
$displayErrors = ($environment === 'dev');

// Container laden
$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config/container.php');
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();
$error_middleware = $app->addErrorMiddleware($displayErrors, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');
$app->add(new AddJsonResponseHeader);

(require __DIR__ . '/config/routes.php')($app);

$app->run();
?>
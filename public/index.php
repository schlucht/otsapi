<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Ots\Bible\Middleware\AddJsonResponseHeader;
use Slim\Factory\AppFactory;
use \Ots\Bible\Middleware\CorsMiddleware;
   
require dirname(__DIR__) . '/public/vendor/autoload.php';

// Container laden
$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/public/config/container.php');
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();
$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');
$app->add(new AddJsonResponseHeader);

(require dirname(__DIR__) . '/public/config/routes.php')($app);

$app->run();
?>
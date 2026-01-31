<?php

declare(strict_types=1);

use Slim\App;
use Ots\API\Controllers\User\UserController;
use Ots\API\Controllers\Bible\BookController;
use Ots\API\Controllers\Bible\TestamentController;
use Ots\API\Controllers\Weather\WeatherController;
use Ots\API\Controllers\User\AuthController;
use Ots\API\Middleware\AuthMiddleware;
use Ots\API\Middleware\RateLimitMiddleware;

return function (App $app) {
    // Get database from container for rate limiting
    $database = $app->getContainer()->get(\Ots\API\Database::class);
    
    // --- Public Routes ---

    // Bible
    $app->get('/api/bible/books', [BookController::class, 'allBooks']);
    $app->get('/api/bible/testaments', [TestamentController::class, 'allTestaments']);

    // Weather
    $app->group('/api/weather', function ($group) {
        $group->get('', [WeatherController::class, 'index']);
        $group->post('', [WeatherController::class, 'store']);
        $group->get('/{id}', [WeatherController::class, 'show']);
    })->add(AuthMiddleware::class);
    
    // Authentication - with rate limiting to prevent brute force
    $app->post('/api/auth/register', [AuthController::class, 'register'])
        ->add(new RateLimitMiddleware($database, 5, 3600)); // 5 registrations per hour
    
    $app->post('/api/auth/login', [AuthController::class, 'login'])
        ->add(new RateLimitMiddleware($database, 5, 300)); // 5 login attempts per 5 minutes
    
    // --- Protected User Routes ---
    $app->group('/api/users', function ($group) {
        $group->get('', [UserController::class, 'allUsers']);
        $group->get('/me', [AuthController::class, 'me']); // Get current user
        $group->get('/{id:[0-9]+}', [UserController::class, 'userById']);
        $group->delete('/{id:[0-9]+}', [UserController::class, 'delete']);
    })->add(AuthMiddleware::class);

};

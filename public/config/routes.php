<?php

declare(strict_types=1);

use Slim\App;
use Ots\API\Controllers\User\UserController;
use Ots\API\Controllers\Bible\BookController;
use Ots\API\Controllers\Bible\TestamentController;
use Ots\API\Controllers\Weather\WeatherController;
use Ots\API\Controllers\User\AuthController;
use Ots\API\Middleware\AuthMiddleware;

return function (App $app) {
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
    
    // Authentication
    $app->post('/api/auth/register', [AuthController::class, 'register']);
    $app->post('/api/auth/login', [AuthController::class, 'login']);
    
    // --- Protected User Routes ---
    $app->group('/api/users', function ($group) {
        $group->get('', [UserController::class, 'allUsers']);
        $group->get('/me', [AuthController::class, 'me']); // Get current user
        $group->get('/{id:[0-9]+}', [UserController::class, 'userById']);
        $group->delete('/{id:[0-9]+}', [UserController::class, 'delete']);
    })->add(AuthMiddleware::class);

};

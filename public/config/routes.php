<?php

declare(strict_types=1);

use Slim\App;
use Ots\Bible\Controllers\User\UserController;
use Ots\Bible\Controllers\Bible\BookController;
use Ots\Bible\Controllers\Bible\TestamentController;
use Ots\Bible\Controllers\Weather\WeatherController;
use Ots\Bible\Controllers\User\AuthController;
use Ots\Bible\Middleware\AuthMiddleware;

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
    });
    
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

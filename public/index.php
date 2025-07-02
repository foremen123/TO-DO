<?php

declare(strict_types=1);

use app\App;
use app\Controllers\HomeController;
use app\Router;
use app\View;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv:: createImmutable ( __DIR__ );
$dotenv -> load ();

const VIEW_PATH = __DIR__ . '/../views/';

$router = new Router();

try {
    $router->registerAttributeRoute(
        [
            HomeController::class
        ]
    );
} catch (ReflectionException $e) {
    echo View::make('Errors/error.500');
}

new App(
    $router,
    [
        'uri' => $_SERVER['REQUEST_URI'],
        'method' => strtolower($_SERVER['REQUEST_METHOD'])
    ]
)->run();
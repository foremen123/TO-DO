<?php

declare(strict_types=1);

session_start();

use app\App;
use app\Config;
use app\Controllers\AuthController;
use app\Controllers\HomeController;
use app\Controllers\NoteController;
use app\DI\Container;
use app\Router;
use app\View;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv:: createImmutable ( dirname(__DIR__ ));
$dotenv -> load ();

const VIEW_PATH = __DIR__ . '/../views/';

$container = new Container();
$router = new Router($container);


try {
    $router->registerAttributeRoute(
        [
            HomeController::class,
            AuthController::class,
            NoteController::class,
        ]
    );
} catch (ReflectionException $e) {
    return View::make('/Errors/Error500');
}

new App(
    $router,
    [
        'uri' => $_SERVER['REQUEST_URI'],
        'method' => strtolower($_SERVER['REQUEST_METHOD'])
    ],
    new Config($_ENV),
)->run();
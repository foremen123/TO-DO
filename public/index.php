<?php

declare(strict_types=1);

session_start();

use app\App;
use app\Config;
use app\Controllers\AuthController;
use app\Controllers\EmailController;
use app\Controllers\HomeController;
use app\Controllers\NoteController;
use app\DI\Container;
use app\Router;
use app\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../vendor/autoload.php';

const VIEW_PATH = __DIR__ . '/../views/';

$container = new Container();
$router = new Router($container);


try {
    $router->registerAttributeRoute(
        [
            HomeController::class,
            AuthController::class,
            NoteController::class,
            EmailController::class
        ]
    );
} catch (ReflectionException $e) {
    http_response_code(503);

    return View::make('/Errors/Error500');
}

new App(
    $container,
    [
        'uri' => $_SERVER['REQUEST_URI'],
        'method' => strtolower($_SERVER['REQUEST_METHOD'])
    ],
    $router
)->boot()->run();
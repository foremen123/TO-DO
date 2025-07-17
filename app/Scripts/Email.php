<?php

declare(strict_types=1);

use app\App;
use app\Config;
use app\Controllers\AuthController;
use app\Controllers\EmailController;
use app\Controllers\HomeController;
use app\Controllers\NoteController;
use app\DI\Container;
use app\Router;
use app\Service\EmailService;
use app\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . '/../../vendor/autoload.php';

$container = new Container();

new App($container)->boot();

$container->get(EmailService::class)->sendQueuedEmails();




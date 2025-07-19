<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Dotenv\Dotenv;


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv:: createImmutable(dirname(__DIR__));
$dotenv->load ();

$dbParams = [
    'driver'   => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_DATABASE'],
    'host'     => $_ENV['DB_HOST'],
];

$connection = DriverManager::getConnection($dbParams);

$builder = $connection->createSchemaManager();

var_dump(array_keys($builder->listTableColumns('notes')));


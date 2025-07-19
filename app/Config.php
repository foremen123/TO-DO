<?php

namespace app;

/**
 * property-read ?array $db
 * @property-read ?array $mailer
 */

class Config
{
    protected array $config = [];

    public function __construct(array $env)
    {
        $this->config =
            [
                'db' =>
                [
                    'driver'   => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                    'user'     => $_ENV['DB_USERNAME'],
                    'password' => $_ENV['DB_PASSWORD'],
                    'dbname'   => $_ENV['DB_DATABASE'],
                    'host'     => $_ENV['DB_HOST'],
                ],
                'mailer' =>
                [
                    'dsn' => $env['MAILER_DSN'],
                ]
            ];
    }

    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }
}
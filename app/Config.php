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
                    'host' => $env['DB_HOST'],
                    'database' => $env['DB_DATABASE'],
                    'username' => $env['DB_USERNAME'],
                    'password' => $env['DB_PASSWORD'],
                    'driver' => $env['DB_DRIVER'] ?? 'mysql',
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
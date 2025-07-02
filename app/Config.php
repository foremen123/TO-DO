<?php

namespace app;

/**
 * property-read ?array $config
 */

class Config
{
    protected array $config = [];

    public function __construct()
    {
        $this->config =
            [
                'db' =>
                [
                    'host' => $_ENV['DB_HOST'],
                    'database' => $_ENV['DB_NAME'],
                    'username' => $_ENV['DB_USERNAME'],
                    'password' => $_ENV['DB_PASSWORD'],
                    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
                ]
            ];
    }
}
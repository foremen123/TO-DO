<?php

declare(strict_types=1);

namespace app;

use PDO;

/**
 * @mixin PDO
 */

class DB
{
    private PDO $pdo;

    /**
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        try {
            $dsn = $config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'];
            $username = $config['username'];
            $password = $config['password'];
            $defaultSettings =
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];
            $this->pdo = new PDO ($dsn, $username, $password, $defaultSettings);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
<?php

declare(strict_types=1);

namespace app;

use app\Models\PdoWrapper;
use app\interface\DatabaseInterface;
use PDO;
use PDOStatement;

/**
 * @mixin PDO
 */

class DB implements DatabaseInterface
{
    private PDO $pdo;
    private pdoWrapper $pdoWrapper;
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
        $this->pdoWrapper = new PdoWrapper($this->pdo);
    }

    public function getPdoWrapper(): DatabaseInterface
    {
        return $this->pdoWrapper;
    }

    public function prepare(string $query): PDOStatement
    {
        return $this->pdo->prepare($query);
    }
}
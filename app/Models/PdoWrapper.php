<?php
namespace app\Models;

use app\interface\DatabaseInterface;
use PDO;

class PdoWrapper implements DatabaseInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function prepare(string $query): \PDOStatement
    {
        return $this->pdo->prepare($query);
    }
}
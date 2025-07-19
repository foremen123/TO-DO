<?php

declare(strict_types=1);

namespace app;

use app\Models\PdoWrapper;
use app\interface\DatabaseInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;


/**
 * @mixin Connection
 */

class DB implements DatabaseInterface
{
    private pdoWrapper $pdoWrapper;
    private Connection $connection;
    public function __construct(array $config)
    {
        try{

            $this->connection = DriverManager::getConnection($config);

        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }
        $this->pdoWrapper = new PdoWrapper($this->connection);
    }

    public function getPdoWrapper(): DatabaseInterface
    {
        return $this->pdoWrapper;
    }

    public function prepare(string $query): Statement
    {
        return $this->connection->prepare($query);
    }

    public function createBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}
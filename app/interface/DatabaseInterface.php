<?php

namespace app\interface;

use PDOStatement;

interface DatabaseInterface
{
    public function prepare(string $query): PDOStatement;
}
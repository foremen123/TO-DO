<?php

namespace app\Models;

use app\App;
use app\interface\DatabaseInterface;
use app\Models;

abstract class Model
{
    public function __construct(protected ?DatabaseInterface $db = null)
    {
        $this->db = $db ?? App::db();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']);
    }
}
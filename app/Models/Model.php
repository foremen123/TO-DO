<?php

namespace app\Models;

use app\App;
use app\DB;

abstract class Model
{

    protected DB $db;

    public function __construct()
    {
        $this->db = App::db();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']);
    }
}
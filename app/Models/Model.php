<?php

namespace app\Models;

use app\App;
use app\Db;

abstract class Model
{
    protected DB $db;

    public function __construct()
    {
        $this->db = App::db();
    }
}
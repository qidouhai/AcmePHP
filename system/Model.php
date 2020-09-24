<?php

namespace NC;

use NC\Core\Database;

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

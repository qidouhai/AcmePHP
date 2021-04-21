<?php

namespace Acme;

use Acme\Core\Medoo;
use App\Config\Database as DatabaseConfig;
use PDO;

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => DatabaseConfig::$dbname,
            'server' => DatabaseConfig::$host,
            'username' => DatabaseConfig::$username,
            'password' => DatabaseConfig::$passswd,
            'charset' => DatabaseConfig::$charset,
            'collation' => 'utf8mb4_general_ci',
            'port' => DatabaseConfig::$port,
            'prefix' => DatabaseConfig::$prefix,
            'logging' => true,
            'socket' => '/tmp/mysql.sock',
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ],
            'command' => [
                'SET SQL_MODE=ANSI_QUOTES'
            ]
        ]);
    }
}

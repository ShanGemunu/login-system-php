<?php

namespace app\database;

use Dotenv\Dotenv;
use mysqli;

class Database
{
    private static $database = null;
    private $conn;

    private function __construct(array $config)
    {
        $this->conn = new mysqli($config['server'], $config['userName'], $config['password'], $config['dbName'], $config['port']);
    }

    /** 
    *  return Database instance, prevent creating more than one Database instance
    *  @param array $config
    *  @return Database   
    */
    public static function getDatabseInstance(array $config) : Database
    {
        if (self::$database === null) {
            self::$database = new Database($config);
        }

        return self::$database;
    }

    /** 
    *   return mysqli db connection object 
    *   @return mysqli
    */
    public function getDbConnection() : mysqli
    {
        return $this->conn;
    }
}

<?php

class Connecter
{
  const SERVER_NAME = "localhost";
  const USER_NAME = "root";
  const PASSWORD = "";
  const DB_NAME = "new_db_login_system";
  const PORT = "49695";


  private $connecter = null;
  private $conn;

  private function __construct()
  {
    $this->conn = new mysqli(self::SERVER_NAME, self::USER_NAME, self::PASSWORD, self::DB_NAME, self::PORT);
  }

  // -> object
  public static function getConneterInstance(){
    if (self::$connecter === null) {
      self::$connecter = new Connecter();
    }

    return self::$connecter;
  }

  public function getDbConnection(){
    return $this->conn;
  }
}

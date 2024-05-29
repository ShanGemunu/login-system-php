<?php
class DbConnection{
  const SERVER_NAME = "localhost";
  const USER_NAME = "root";
  const PASSWORD = "";
  const DB_NAME = "new_db_login_system";
  const PORT = "33061";
  
  // DB connection 
  public $conn;  // ??
  
  function __construct(){
    try{
      $this->conn = new mysqli(self::SERVER_NAME, self::USER_NAME, self::PASSWORD, self::DB_NAME, self::PORT); 
    }catch(Exception $e){
      $this->conn = null;      
    }
  }

  function __destruct(){
    $this->conn->close();  
  }
}

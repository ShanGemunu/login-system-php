<?php
class DbConnection{
  const SERVER_NAME = "localhost";
  const USER_NAME = "root";
  const PASSWORD = "";
  const DB_NAME = "loginsystem_php";
  const PORT = "3307";
  
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
    $message = "DB connection closed!";
    echo "<script>console.log('" . addslashes($message) . "');</script>";
    if($this->conn) $this->conn->close();  
  }
}

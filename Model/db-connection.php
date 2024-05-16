<?php
class CreateDbConnection{
  private const SERVER_NAME = "localhost";
  private const USER_NAME = "root";
  private const PASSWORD = "";
  private const DB_NAME = "loginsystem_php";
  private const PORT = "3307";
  
  // DB connection 
  public $conn;  // ??
  
  function __construct(){
    try{
      $this->conn = new mysqli(self::SERVER_NAME, self::USER_NAME, self::PASSWORD, self::DB_NAME, self::PORT); 
    }catch(Exception $e){
      $this->conn = null;      
    }
  }

  function terminateConnection(){
    $this->conn->close();
  }
  
}

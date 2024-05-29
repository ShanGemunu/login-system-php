<?php
require_once('Model/DbConnection.php');
require_once('Model/Queries.php') ;

class Login{
  public $isValidUser;
  public $result;

  function __construct($email, $password){
    $conn = new DbConnection(); // create object to get db connection
    $resultArray = null;

    if($conn->conn){
      $resultArray = $this->authenticateUser($conn, $email, $password);

    }else{
      $this->result = "DB connection failed and couldn't log in, try again!";
      $this->isValidUser = false;
      return; 
    }
  
    $this->isValidUser = $resultArray[0];  // true or false
    $this->result = $resultArray[1];   // result from authenticateUser
  }

  function authenticateUser($conn, $email, $password){
    $queries = new Queries($conn->conn);
    
    // check if provided email was already registered
    $tempResult = $queries->checkUserIsExist($email);
    if($tempResult->num_rows === 0){
      $result = "Login falied, there is no user registered to this email.";                                
      return [false, $result];
    }
    
    // check password
    $row = $tempResult->fetch_assoc();
    if(password_verify($password, $row["hashed_password"])){
      return [true, $row["user_name"]]; // return -> true, authenticated user name
    }else{
      $result = "Login failed, password is wrong.";
      return [false, $result];                              
    }
  }
  
}








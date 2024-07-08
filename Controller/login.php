<?php
require_once('Model/DbConnection.php');
require_once('Model/Queries.php') ;

class Login{
  public $isValidUser;
  public $result;

  public $userEmailAndType;

  function __construct($email, $password){
    $conn = new DbConnection(); // create object to get db connection
    $resultArray = null;

    if($conn->conn){
      $resultArray = $this->authenticateUser($conn, $email, $password);

    }else{
      $this->result = "DB connection failed and couldn't log in, try again!";
      $this->isValidUser = false;
      $this->userEmailAndType = [null,null];
      return; 
    }
  
    $this->isValidUser = $resultArray[0];  // true or false
    $this->result = $resultArray[1];   // result status from authenticateUser
    $this->userEmailAndType = $resultArray[2]; // user email and type
  }
  
  // object(DbConnection, string, string) -> array [bool,string,string]
  function authenticateUser($conn, $email, $password){
    if(30<strlen($email) or 20<strlen($password)){
      return [false, "invalid inputs", null];
    }

    $queries = new Queries($conn->conn);

    // check if provided email was already registered
    $tempResult = $queries->checkUserIsExist($email);
    if($tempResult->num_rows === 0){
      $result = "Login falied, there is no user registered to this email.";                                
      return [false, $result, null];
    }
    
    // check password
    $row = $tempResult->fetch_assoc();
    if(password_verify($password, $row["hashed_password"])){
      return [true, "success", [$row["email"], $row["user_type"]]]; // return -> true, authenticated user email, user type
    }else{
      $result = "Login failed, password is wrong.";
      return [false, $result, null];                              
    }
  }
  
}








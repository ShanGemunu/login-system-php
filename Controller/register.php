<?php
require_once('Model/DbConnection.php');
require_once('Model/Queries.php') ;

class Register{
  public $isUserRegistered;
  public $result;
  
  public function __construct($userName, $email, $password){  
    $conn = new DbConnection(); // create object to get db connection
    $resultArray = null;

    if($conn->conn){
      $resultArray = $this->registerNewUser($conn, $userName, $email, $password);

    }else{
      $this->isUserRegistered = false;
      $this->result = "DB connection failed and couldn't log in, try again!";
      return; 
    }

    $this->isUserRegistered = $resultArray[0];  // true or false
    $this->result = $resultArray[1];    // result from registerNewUser
  }

  function registerNewUser($conn, $userName, $email, $password){

    if(20<strlen($userName) or 20<strlen($email) or 20<strlen($password)){
      return [false, "invalid inputs"];
    }
    
    // validate user inputs
    if($userName and $email and filter_var($email, FILTER_VALIDATE_EMAIL) and $password){
      
      $queries = new Queries($conn->conn);

      // check if new provided email is already registered
      $tempResult = $queries->checkUserIsExist($email);
      if($tempResult->num_rows > 0){
        return [false, "Email provided already registered, try different email."];
      }
      
      // if no existing user email equal to inputed user email then continue
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      
      $userObejct = $queries->insertNewUser($email, $hashedPassword);
  
      if ($userObejct){
        return [true, $email];
      }else{ 
        return [false, "Something went wrong, couldn't register user!"];
      }
  
    }else{
      return [false, "Make sure to fill all the required fields OR provide vaild email."];
    }
  }
}










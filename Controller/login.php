<?php
include '../Model/db-connection.php';
include '../Model/queries.php';

session_start();

function authenticateUser($conn){
  $queries = new Queries();
  
  // check if provided email was already registered
  $tempResult = $queries->checkUserIsExist($conn->conn, $_POST['email']);
  if($tempResult->num_rows === 0){
    $result = htmlspecialchars("Login falied, there is no user registered to this email.");
    header("Location: ../View/login-page.php?result=".urlencode($result));                                
    return null;
  }
  
  // check password
  $row = $tempResult->fetch_assoc();
  if(password_verify($_POST['password'], $row["hashed_password"])){
    $_SESSION['currentUser'] = $row["user_name"];
    header("Location: ../View/home-page.php");
  }else{
    $result = htmlspecialchars("Login failed, password is wrong.");
    header("Location: ../View/login-page.php?result=".urlencode($result));                               
  }
}

if(isset($_POST['login_button'])){
  $conn = new DbConnection(); // create object to get db connection
  if($conn->conn){
    authenticateUser($conn);
    $conn->terminateConnection();
  }else{
    $result = htmlspecialchars("DB connection failed and couldn't log in, try again!");
    header("Location: ../View/login-page.php?result=".urlencode($result));  
  }
}






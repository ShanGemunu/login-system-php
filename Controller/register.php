<?php
include '../Model/db-connection.php';
include '../Model/queries.php';
session_start();

function registerNewUser($conn){
  
  // validate user inputs
  if($_POST['user_name'] and $_POST['email'] and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and $_POST['password']){
    
    $queries = new Queries($conn->conn);
    // check if new provided email is already registered
    $tempResult = $queries->checkUserIsExist($_POST['email']);
    if($tempResult->num_rows > 0){
      $result = htmlspecialchars("Email provided already registered, try different email.");
      header("Location: ../View/register-page.php?result=".urlencode($result));  
      return null;
    }
    
    // if no existing user email equal to inputed user email then continue
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $userObejct = $queries->insertNewUser($_POST['email'], $hashedPassword);

    if ($userObejct){
      $_SESSION['currentUser'] = $_POST['user_name'];
      header("Location: ../View/home-page.php");
    }else{
      $result = htmlspecialchars("Something went wrong, couldn't register user!");
      header("Location: ../View/register-page.php?result=".urlencode($result)); 
    }

  }else{
    $result = htmlspecialchars("Make sure to fill all the required fields OR provide vaild email.");
    header("Location: ../View/register-page.php?result=".urlencode($result));
  }
}

//get db connection and continue script if get working connection
function getDbConnection(){
  $conn = new DbConnection(); // create object to get db connection
  if($conn->conn){
    registerNewUser($conn);
    $conn->terminateConnection();
  }else{
    $result = htmlspecialchars("DB connection falied and Couldn't register user. try again.");
    header("Location: ../View/register-page.php?result=".urlencode($result));
  }
}

if(isset($_POST["register_button"])){
  
  // check if user already logged in
  if(isset($_SESSION["currentUser"])){
    $result = htmlspecialchars("Already user logged in. To register new user first log out curent user.");
    header("Location: ../View/register-page.php?result=".urlencode($result));     
  }else{
    getDbConnection();
  }
}




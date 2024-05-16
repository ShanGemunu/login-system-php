<?php
include '../Model/db-connection.php';
include '../Model/queries.php';
include '../View/register-page.php';
session_start();

$result = null;

function registerNewUser($conn){
  
  // validate user inputs
  if($_POST['user_name'] and $_POST['email'] and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and $_POST['password']){
    
    // check if existing user email in DB equal to new input user email
    $tempResult = checkUserIsExist($conn->conn, $_POST['email']);
    if($tempResult->num_rows > 0){
      return "<p>Email provided already registered, try different email.</p>";
    }
    
    // if no existing user email equal to inputed user email then continue
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $userObejct = insertNewUser($conn->conn, $_POST['email'], $hashedPassword);

    if ($userObejct){
      $_SESSION['currentUser'] = $_POST['user_name'];
      header("Location: homepage.php");
    }else{
      return "<p>Something went wrong, couldn't register user!</p>";
    }

  }else{
    return "<p>Make sure to fill all the required fields OR provide vaild email.</p>";
  }
}

//get db connection and continue script if get working connection
function getDbConnection(){
  $conn = new CreateDbConnection(); // create object to get db connection
  if($conn->conn){
    $output = registerNewUser($conn);
    $conn->terminateConnection();
    return $output;
  }else{
    return "<p>DB connection failed and couldn't log in, try again!</p>";
  }
}

if(isset($_POST["register_button"])){
  
  // check if user already logged in
  if(isset($_SESSION["currentUser"])){
    $result = "<p>Already user logged in. To register new user first log out curent user.</p>";
  }else{
    $result = getDbConnection();
  }
}

// output html page
echo generateHTMLPage($result);


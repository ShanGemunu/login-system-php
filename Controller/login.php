<?php
include '../Model/db-connection.php';
include '../Model/queries.php';
include '../View/login-page.php';

session_start();

// check if user already logged in
if(isset($_SESSION["currentUser"])){
  header("Location: homepage.php");
  exit(0);
}

$result = null;

function authenticateUser($conn){
  
  // check if provided email was already registered
  $tempResult = checkUserIsExist($conn->conn, $_POST['email']);
  if($tempResult->num_rows === 0){
    return "<p>Login falied, there is no user registered to this email.</p>";
  }
  
  // check password
  $row = $tempResult->fetch_assoc();
  if(password_verify($_POST['password'], $row["hashed_password"])){
    $_SESSION['currentUser'] = $row["user_name"];
    header("Location: homepage.php");
  }else{
    return  "<p>Login failed, password is wrong.</p>";
  }
}

if(isset($_POST['login_button'])){
  $conn = new CreateDbConnection(); // create object to get db connection
  if($conn->conn){
    $result = authenticateUser($conn);
    $conn->terminateConnection();
  }else{
    $result = "<p>DB connection failed and couldn't log in, try again!</p>";
  }
}

// output html page
echo generateHTMLPage($result);




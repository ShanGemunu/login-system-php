<?php
include 'db-connection.php';
session_start();

$result = null;

function registerNewUser(){
  global $servername, $username, $password, $dbName, $port;

  // chreck if user already logged in
  if(isset($_SESSION["currentUser"])){
    echo "<h4>Already user logged in. To register new user first log out curent user.</h4>";
    return null;
  }
  
  // validate user inputs
  if($_POST['user-name'] and $_POST['email'] and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and $_POST['password']){
    
    // Create db connection
    try{
      $conn = new mysqli($servername, $username, $password, $dbName, $port);
    }catch(Exception $e){
      echo "<h4>DB connection falied and couldn't register user!</h4>";
      return null;
    }
    
    // check if existing user email in DB as new input user email
    $checkQuery = "SELECT * FROM users WHERE email='".$_POST['email']."'";
    $tempResult = $conn -> query($checkQuery);
    if($tempResult->num_rows > 0){
      echo "<h4>Email provided already registered, try different email.</h4>";
      $conn->close();
      return null;
    }
    

    // if no existing user email as inputed user email then continue
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $insertQuery = "INSERT INTO users (user_name, email, hashed_password)
    VALUES ('".$_POST['user-name']."','".$_POST['email']."','".$hashedPassword."')";

    if ($conn -> query($insertQuery)) {
      $conn->close();
      $_SESSION['currentUser'] = $_POST['user-name'];
      header("Location: /demo/login-system-php/homepage.php");
    }else{
      echo "<h4>Something went wrong, couldn't register user!</h4>";
    }

    $conn->close();
  }else{
    echo "<h4>Make sure to fill all the required fields OR provide vaild email.</h4>";
  }
}

if(isset($_POST["register-button"])){
  registerNewUser();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form action="" method="post" action="register.php">
    <label for="">User Name</label>
    <input type="text" name="user-name" id=""><br>
    <label for="">Email</label>
    <input type="email" name="email" id=""><br>
    <label for="">Password</label>
    <input type="password" name="password" id="">
    <button type="submit" name="register-button">Register</button>
  </form>
</body>
</html>
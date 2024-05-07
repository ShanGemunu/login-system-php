<?php
session_start();

$result = null;

function authenticateUser(){
  // chreck if user already logged in
  if(isset($_SESSION["currentUser"])){
    echo "<h4>Already user logged in. To log in another user first log out curent user.</h4>";
    return null;
  }

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbName = "loginsystem_php";
  $port = "3307";

  // Create db connection
  try{
    $conn = new mysqli($servername, $username, $password, $dbName, $port);
  }catch(Exception $e){
    echo "<h4>DB connection failed and couldn't log in, try again!</h4>";
    return null;
  }
  
  $checkQuery = "SELECT user_name, hashed_password FROM users WHERE email='".$_POST['email']."'";
  $tempResult = $conn -> query($checkQuery);
  if($tempResult->num_rows === 0){
    echo "<h4>Login falied, there is no user registered to this email.</h4>";
    $conn->close();
    return null;
  }

  $row = $tempResult->fetch_assoc();
  if(password_verify($_POST['password'], $row["hashed_password"])){
  $_SESSION['currentUser'] = $row["user_name"];
  header("Location: /demo/login-system-php/homepage.php");
  }else{
  echo "<h4>Login failed, password is wrong.</h4>";
  }

  $conn->close();
}

if(isset($_POST['login-button'])){
  authenticateUser();
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
  <form action="login.php" method="post">
    <label for="">Email</label>
    <input type="text" name="email" id=""><br>
    <label for="">Password</label>
    <input type="password" name="password" id="">
    <button type="submit" name="login-button">Log in</button>
  </form>
</body>
</html>
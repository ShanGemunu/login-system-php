<?php
include 'db-connection.php';

session_start();

$result = null;

function authenticateUser(){
  global $result;
  // chreck if user already logged in
  if(isset($_SESSION["currentUser"])){
    $result = "<p>Already user logged in. To log in another user first log out curent user.</p>";
    return null;
  }

  global $servername, $username, $password, $dbName, $port;

  // Create db connection
  try{
    $conn = new mysqli($servername, $username, $password, $dbName, $port);
  }catch(Exception $e){
    $result =  "<p>DB connection failed and couldn't log in, try again!</p>";
    return null;
  }
  
  $checkQuery = "SELECT user_name, hashed_password FROM users WHERE email='".$_POST['email']."'";
  $tempResult = $conn -> query($checkQuery);
  if($tempResult->num_rows === 0){
    $result = "<p>Login falied, there is no user registered to this email.</p>";
    $conn->close();
    return null;
  }

  $row = $tempResult->fetch_assoc();
  if(password_verify($_POST['password'], $row["hashed_password"])){
  $_SESSION['currentUser'] = $row["user_name"];
  header("Location: /demo/login-system-php/homepage.php");
  }else{
  $result =  "<p>Login failed, password is wrong.</p>";
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
    <title>Log in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-3 m-0 border-0 m-0 border-0">
  
  <div class="d-flex align-items-center justify-content-center">
    <form action="login.php" method="post" class="w-25 border p-3 rounded">
      <h4 class="">Login here</h4><br>
      <div class="mb-3">
        <label for="" class="form-label">Email</label>
        <input type="text" name="email" id="" class="form-control">  
      </div>
      <div>
        <label for="" class="form-label">Password</label>
        <input class="form-control" type="password" name="password" id="">
      </div><br>
      <button type="submit" name="login-button" class="btn btn-primary">Log in</button>
    </form>
  </div>
  <div class="d-flex align-items-center justify-content-center mt-4 text-danger">
    <?php echo $result; ?>
  </div>
</body>
</html>